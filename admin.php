<?php
include 'config.php';

$success_msg = '';
$error_msg = '';
$edit_data = null;

// ── HANDLE IMAGE UPLOAD ──────────────────────────────────
function handleImageUpload() {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $ftype = $_FILES['image']['type'];
        if (!in_array($ftype, $allowed)) return ['error' => 'Only JPG, PNG, GIF, WEBP images allowed.'];

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = 'doc_' . time() . '_' . rand(100,999) . '.' . $ext;
        $dest = 'uploads/' . $filename;

        if (!is_dir('uploads')) mkdir('uploads', 0755, true);

        if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
            return ['filename' => $filename];
        }
        return ['error' => 'Failed to save image.'];
    }
    return ['filename' => null];
}

// ── ADD DOCTOR ───────────────────────────────────────────
if (isset($_POST['add_doctor'])) {
    $name   = mysqli_real_escape_string($conn, trim($_POST['name']));
    $spec   = mysqli_real_escape_string($conn, trim($_POST['specialization']));
    $phone  = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $email  = mysqli_real_escape_string($conn, trim($_POST['email']));
    $exp    = (int)$_POST['experience'];
    $days   = mysqli_real_escape_string($conn, trim($_POST['available_days']));

    if (!$name || !$spec || !$phone || !$email) {
        $error_msg = 'Please fill in all required fields.';
    } else {
        $img_result = handleImageUpload();
        if (isset($img_result['error'])) {
            $error_msg = $img_result['error'];
        } else {
            $image = $img_result['filename'] ?? '';
            $sql = "INSERT INTO doctors (name, specialization, phone, email, experience, available_days, image)
                    VALUES ('$name','$spec','$phone','$email',$exp,'$days','$image')";
            if (mysqli_query($conn, $sql)) {
                $success_msg = "Dr. $name added successfully!";
            } else {
                $error_msg = 'Database error: ' . mysqli_error($conn);
            }
        }
    }
}

// ── UPDATE DOCTOR ────────────────────────────────────────
if (isset($_POST['update_doctor'])) {
    $id     = (int)$_POST['id'];
    $name   = mysqli_real_escape_string($conn, trim($_POST['name']));
    $spec   = mysqli_real_escape_string($conn, trim($_POST['specialization']));
    $phone  = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $email  = mysqli_real_escape_string($conn, trim($_POST['email']));
    $exp    = (int)$_POST['experience'];
    $days   = mysqli_real_escape_string($conn, trim($_POST['available_days']));
    $old_image = mysqli_real_escape_string($conn, $_POST['old_image']);

    $img_result = handleImageUpload();
    if (isset($img_result['error'])) {
        $error_msg = $img_result['error'];
    } else {
        $image = $img_result['filename'] ?? $old_image;

        // Delete old image if new one uploaded
        if ($img_result['filename'] && $old_image && file_exists('uploads/' . $old_image)) {
            unlink('uploads/' . $old_image);
        }

        $sql = "UPDATE doctors SET name='$name', specialization='$spec', phone='$phone',
                email='$email', experience=$exp, available_days='$days', image='$image'
                WHERE id=$id";
        if (mysqli_query($conn, $sql)) {
            $success_msg = "Dr. $name updated successfully!";
        } else {
            $error_msg = 'Database error: ' . mysqli_error($conn);
        }
    }
}

// ── DELETE DOCTOR ────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT image FROM doctors WHERE id=$id"));
    if ($row && $row['image'] && file_exists('uploads/' . $row['image'])) {
        unlink('uploads/' . $row['image']);
    }
    mysqli_query($conn, "DELETE FROM doctors WHERE id=$id");
    $success_msg = 'Doctor removed successfully.';
}

// ── FETCH FOR EDIT ───────────────────────────────────────
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM doctors WHERE id=$id");
    $edit_data = mysqli_fetch_assoc($res);
}

// ── FETCH ALL ────────────────────────────────────────────
$all = mysqli_query($conn, "SELECT * FROM doctors ORDER BY id DESC");
$total = mysqli_num_rows($all);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel — MediCare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --navy:       #0b1f3a;
            --navy-mid:   #122945;
            --teal:       #0d9488;
            --teal-light: #14b8a8;
            --cream:      #f8f4ef;
            --white:      #ffffff;
            --gray:       #94a3b8;
            --red:        #ef4444;
            --red-soft:   #fee2e2;
            --green:      #10b981;
            --green-soft: #d1fae5;
            --text:       #1e293b;
            --border:     #e2e8f0;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #f1f5f9;
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── HEADER ── */
        header {
            background: var(--navy);
            padding: 0 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 70px;
            position: sticky; top: 0; z-index: 100;
            box-shadow: 0 2px 20px rgba(0,0,0,0.3);
        }
        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: var(--white);
        }
        .logo span { color: var(--teal-light); }
        .header-right { display: flex; align-items: center; gap: 16px; }
        .admin-label {
            background: rgba(13,148,136,0.2);
            border: 1px solid rgba(13,184,168,0.3);
            color: var(--teal-light);
            font-size: 0.78rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 5px 14px;
            border-radius: 20px;
        }
        nav a {
            color: #cbd5e1;
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 500;
            transition: color .2s;
        }
        nav a:hover { color: var(--teal-light); }

        /* ── PAGE LAYOUT ── */
        .page-wrap {
            max-width: 1300px;
            margin: 36px auto;
            padding: 0 4%;
            display: grid;
            grid-template-columns: 400px 1fr;
            gap: 28px;
            flex: 1;
        }

        /* ── PANEL / CARDS ── */
        .panel {
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(11,31,58,0.08);
            overflow: hidden;
        }
        .panel-header {
            background: var(--navy);
            padding: 20px 28px;
            display: flex; align-items: center; gap: 12px;
        }
        .panel-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.15rem;
            color: var(--white);
            font-weight: 600;
        }
        .panel-header .panel-icon {
            width: 36px; height: 36px;
            background: rgba(13,184,168,0.2);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
        }
        .panel-body { padding: 28px; }

        /* ── MESSAGES ── */
        .msg {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 0.88rem;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex; align-items: center; gap: 10px;
        }
        .msg-success { background: var(--green-soft); color: #065f46; border: 1px solid #6ee7b7; }
        .msg-error   { background: var(--red-soft);   color: #991b1b; border: 1px solid #fca5a5; }

        /* ── FORM ── */
        .form-grid { display: flex; flex-direction: column; gap: 16px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

        label {
            font-size: 0.78rem;
            font-weight: 600;
            color: #475569;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        label .req { color: var(--red); margin-left: 2px; }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            color: var(--text);
            background: #fafbfc;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }
        input:focus, textarea:focus {
            border-color: var(--teal);
            box-shadow: 0 0 0 3px rgba(13,148,136,0.12);
            background: var(--white);
        }

        .upload-zone {
            border: 2px dashed var(--border);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: border-color .2s, background .2s;
            position: relative;
        }
        .upload-zone:hover { border-color: var(--teal); background: #f0fdfa; }
        .upload-zone input[type="file"] {
            position: absolute; inset: 0;
            opacity: 0; cursor: pointer;
            width: 100%; height: 100%;
        }
        .upload-icon { font-size: 1.8rem; margin-bottom: 6px; }
        .upload-text { font-size: 0.82rem; color: var(--gray); }

        .preview-wrap {
            margin-top: 10px;
            display: none;
            text-align: center;
        }
        .preview-wrap img {
            width: 80px; height: 80px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid var(--teal);
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-primary { background: var(--teal); color: white; }
        .btn-primary:hover { background: var(--teal-light); transform: translateY(-1px); }
        .btn-secondary { background: #f1f5f9; color: #475569; }
        .btn-secondary:hover { background: #e2e8f0; }
        .btn-cancel { background: #f1f5f9; color: var(--text); text-decoration: none; }
        .form-actions { display: flex; gap: 10px; margin-top: 8px; }

        /* ── TABLE PANEL ── */
        .table-header {
            padding: 20px 28px;
            display: flex; align-items: center; justify-content: space-between;
            border-bottom: 1px solid var(--border);
        }
        .table-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.15rem;
            color: var(--navy);
        }
        .total-badge {
            background: var(--teal);
            color: white;
            font-size: 0.78rem;
            font-weight: 700;
            padding: 4px 14px;
            border-radius: 20px;
        }

        .table-wrap { overflow-x: auto; }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.87rem;
        }
        thead tr {
            background: #f8fafc;
            border-bottom: 2px solid var(--border);
        }
        th {
            padding: 13px 16px;
            text-align: left;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--gray);
        }
        tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background .15s;
        }
        tbody tr:hover { background: #f8fafc; }
        td { padding: 14px 16px; vertical-align: middle; }

        .td-avatar {
            width: 42px; height: 42px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--border);
        }
        .td-avatar-placeholder {
            width: 42px; height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #dbeafe, #d1faf4);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
            border: 2px solid var(--border);
        }

        .doctor-cell { display: flex; align-items: center; gap: 12px; }
        .doctor-name-td { font-weight: 600; color: var(--navy); }
        .doctor-spec-td { font-size: 0.75rem; color: var(--gray); }

        .spec-pill {
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 0.72rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            white-space: nowrap;
        }
        .exp-pill {
            background: #f0fdf4;
            color: #15803d;
            font-size: 0.72rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
        }

        .action-btns { display: flex; gap: 8px; }
        .btn-edit {
            padding: 6px 14px;
            background: #eff6ff;
            color: #1d4ed8;
            border: none; border-radius: 6px;
            font-size: 0.8rem; font-weight: 600;
            text-decoration: none;
            transition: background .2s;
            cursor: pointer;
        }
        .btn-edit:hover { background: #dbeafe; }
        .btn-delete {
            padding: 6px 14px;
            background: var(--red-soft);
            color: var(--red);
            border: none; border-radius: 6px;
            font-size: 0.8rem; font-weight: 600;
            text-decoration: none;
            transition: background .2s;
            cursor: pointer;
        }
        .btn-delete:hover { background: #fecaca; }

        /* ── EMPTY TABLE ── */
        .empty-table {
            text-align: center;
            padding: 60px 20px;
            color: var(--gray);
        }
        .empty-table .e-icon { font-size: 3rem; margin-bottom: 12px; }
        .empty-table p { font-size: 0.9rem; }

        /* ── EDIT MODE BANNER ── */
        .edit-banner {
            background: linear-gradient(135deg, #eff6ff, #f0fdf4);
            border: 1.5px solid #93c5fd;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.85rem;
            color: #1e40af;
            margin-bottom: 20px;
            display: flex; align-items: center; gap: 10px;
        }

        @media (max-width: 900px) {
            .page-wrap { grid-template-columns: 1fr; }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<header>
    <div class="logo">Medi<span>Care</span></div>
    <div class="header-right">
        <span class="admin-label">Admin</span>
        <nav><a href="index.php">← View Homepage</a></nav>
    </div>
</header>

<div class="page-wrap">

    <!-- ── LEFT: ADD / EDIT FORM ── -->
    <div>
        <div class="panel">
            <div class="panel-header">
                <div class="panel-icon"><?= $edit_data ? '✏️' : '➕' ?></div>
                <h2><?= $edit_data ? 'Edit Doctor' : 'Add New Doctor' ?></h2>
            </div>
            <div class="panel-body">

                <?php if ($success_msg): ?>
                <div class="msg msg-success">✅ <?= $success_msg ?></div>
                <?php endif; ?>
                <?php if ($error_msg): ?>
                <div class="msg msg-error">❌ <?= $error_msg ?></div>
                <?php endif; ?>

                <?php if ($edit_data): ?>
                <div class="edit-banner">✏️ Editing: <strong>Dr. <?= htmlspecialchars($edit_data['name']) ?></strong></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="form-grid">
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                        <input type="hidden" name="old_image" value="<?= htmlspecialchars($edit_data['image'] ?? '') ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Full Name <span class="req">*</span></label>
                        <input type="text" name="name" placeholder="e.g. Sarah Johnson"
                            value="<?= htmlspecialchars($edit_data['name'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Specialization <span class="req">*</span></label>
                        <input type="text" name="specialization" placeholder="e.g. Cardiologist"
                            value="<?= htmlspecialchars($edit_data['specialization'] ?? '') ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Phone <span class="req">*</span></label>
                            <input type="text" name="phone" placeholder="+60 12-345 6789"
                                value="<?= htmlspecialchars($edit_data['phone'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Experience (years)</label>
                            <input type="number" name="experience" min="0" max="60"
                                value="<?= htmlspecialchars($edit_data['experience'] ?? '0') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Email <span class="req">*</span></label>
                        <input type="email" name="email" placeholder="doctor@medicare.com"
                            value="<?= htmlspecialchars($edit_data['email'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Available Days</label>
                        <input type="text" name="available_days" placeholder="Mon, Wed, Fri"
                            value="<?= htmlspecialchars($edit_data['available_days'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Doctor Photo</label>
                        <div class="upload-zone" id="uploadZone">
                            <input type="file" name="image" accept="image/*" id="imgInput"
                                onchange="previewImage(this)">
                            <div class="upload-icon">📷</div>
                            <div class="upload-text">Click to upload photo (JPG, PNG, GIF)</div>
                        </div>
                        <div class="preview-wrap" id="previewWrap">
                            <img id="previewImg" src="#" alt="Preview">
                        </div>
                        <?php if ($edit_data && !empty($edit_data['image']) && file_exists('uploads/'.$edit_data['image'])): ?>
                            <div style="margin-top:10px; text-align:center;">
                                <img src="uploads/<?= htmlspecialchars($edit_data['image']) ?>"
                                    style="width:70px;height:70px;border-radius:50%;object-fit:cover;border:2px solid var(--teal);">
                                <div style="font-size:0.75rem;color:var(--gray);margin-top:4px;">Current photo — upload new to replace</div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-actions">
                        <?php if ($edit_data): ?>
                            <button type="submit" name="update_doctor" class="btn btn-primary">💾 Save Changes</button>
                            <a href="admin.php" class="btn btn-cancel">✕ Cancel</a>
                        <?php else: ?>
                            <button type="submit" name="add_doctor" class="btn btn-primary">➕ Add Doctor</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ── RIGHT: DOCTORS TABLE ── -->
    <div>
        <div class="panel">
            <div class="table-header">
                <h2>All Doctors</h2>
                <span class="total-badge"><?= $total ?> Doctor<?= $total != 1 ? 's' : '' ?></span>
            </div>

            <?php if ($total > 0): ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Doctor</th>
                            <th>Specialization</th>
                            <th>Contact</th>
                            <th>Exp</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = mysqli_fetch_assoc($all)): ?>
                        <tr>
                            <td>
                                <div class="doctor-cell">
                                    <?php if (!empty($row['image']) && file_exists('uploads/'.$row['image'])): ?>
                                        <img src="uploads/<?= htmlspecialchars($row['image']) ?>" class="td-avatar" alt="">
                                    <?php else: ?>
                                        <div class="td-avatar-placeholder">👨‍⚕️</div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="doctor-name-td">Dr. <?= htmlspecialchars($row['name']) ?></div>
                                        <div class="doctor-spec-td"><?= htmlspecialchars($row['email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="spec-pill"><?= htmlspecialchars($row['specialization']) ?></span></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td><span class="exp-pill"><?= $row['experience'] ?> yrs</span></td>
                            <td>
                                <div class="action-btns">
                                    <a href="admin.php?edit=<?= $row['id'] ?>" class="btn-edit">✏️ Edit</a>
                                    <a href="admin.php?delete=<?= $row['id'] ?>" class="btn-delete"
                                        onclick="return confirm('Remove Dr. <?= htmlspecialchars(addslashes($row['name'])) ?>?')">🗑 Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="empty-table">
                <div class="e-icon">🏥</div>
                <p>No doctors added yet. Use the form to add the first one!</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

</div><!-- /page-wrap -->

<script>
function previewImage(input) {
    const wrap = document.getElementById('previewWrap');
    const img  = document.getElementById('previewImg');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            wrap.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

</body>
</html>