<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediCare — Doctor Directory</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --navy:   #0b1f3a;
            --teal:   #0d9488;
            --teal-light: #14b8a8;
            --cream:  #f8f4ef;
            --white:  #ffffff;
            --gray:   #94a3b8;
            --text:   #1e293b;
            --card-shadow: 0 4px 24px rgba(11,31,58,0.10);
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--cream);
            color: var(--text);
            min-height: 100vh;
        }

        /* ── HEADER ── */
        header {
            background: var(--navy);
            padding: 0 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 72px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 20px rgba(0,0,0,0.25);
        }
        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            color: var(--white);
            letter-spacing: -0.5px;
        }
        .logo span { color: var(--teal-light); }
        nav a {
            color: #cbd5e1;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            margin-left: 32px;
            transition: color .2s;
        }
        nav a:hover, nav a.active { color: var(--teal-light); }
        .admin-btn {
            background: var(--teal);
            color: var(--white) !important;
            padding: 8px 20px;
            border-radius: 6px;
            transition: background .2s !important;
        }
        .admin-btn:hover { background: var(--teal-light) !important; }

        /* ── HERO ── */
        .hero {
            background: linear-gradient(135deg, var(--navy) 0%, #163d6e 60%, #0d4a5e 100%);
            padding: 80px 5% 70px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(13,148,136,0.18) 0%, transparent 70%);
            top: -100px; right: -100px;
            pointer-events: none;
        }
        .hero-badge {
            display: inline-block;
            background: rgba(13,184,168,0.15);
            border: 1px solid rgba(13,184,168,0.4);
            color: var(--teal-light);
            font-size: 0.78rem;
            font-weight: 500;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 6px 18px;
            border-radius: 20px;
            margin-bottom: 20px;
        }
        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2rem, 5vw, 3.2rem);
            color: var(--white);
            line-height: 1.2;
            margin-bottom: 16px;
        }
        .hero p {
            color: #94a3b8;
            font-size: 1.05rem;
            max-width: 500px;
            margin: 0 auto;
        }

        /* ── STATS BAR ── */
        .stats-bar {
            background: var(--white);
            display: flex;
            justify-content: center;
            gap: 60px;
            padding: 22px 5%;
            border-bottom: 1px solid #e2e8f0;
        }
        .stat { text-align: center; }
        .stat-number {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            color: var(--teal);
            font-weight: 700;
            line-height: 1;
        }
        .stat-label {
            font-size: 0.78rem;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 4px;
        }

        /* ── MAIN CONTENT ── */
        main {
            max-width: 1280px;
            margin: 0 auto;
            padding: 56px 5%;
        }
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 36px;
        }
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.7rem;
            color: var(--navy);
        }
        .section-title span {
            display: block;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.82rem;
            color: var(--gray);
            font-weight: 400;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .count-badge {
            background: var(--teal);
            color: white;
            font-size: 0.82rem;
            font-weight: 600;
            padding: 6px 16px;
            border-radius: 20px;
        }

        /* ── DOCTOR GRID ── */
        .doctor-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 28px;
        }

        .doctor-card {
            background: var(--white);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: transform .25s ease, box-shadow .25s ease;
            animation: fadeUp .5s ease both;
        }
        .doctor-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 40px rgba(11,31,58,0.16);
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .card-image-wrap {
            position: relative;
            height: 220px;
            background: linear-gradient(135deg, #e2f0f9, #d1faf4);
            overflow: hidden;
        }
        .card-image-wrap img {
            width: 100%; height: 100%;
            object-fit: cover;
            transition: transform .4s ease;
        }
        .doctor-card:hover .card-image-wrap img { transform: scale(1.05); }
        .card-image-placeholder {
            width: 100%; height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 5rem;
            background: linear-gradient(135deg, #dbeafe, #d1faf4);
        }
        .spec-tag {
            position: absolute;
            bottom: 14px; left: 14px;
            background: var(--navy);
            color: var(--teal-light);
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            padding: 5px 12px;
            border-radius: 20px;
        }

        .card-body { padding: 22px 24px 24px; }
        .doctor-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem;
            color: var(--navy);
            margin-bottom: 14px;
        }

        .info-list { list-style: none; display: flex; flex-direction: column; gap: 8px; }
        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
            color: #475569;
        }
        .info-icon {
            width: 28px; height: 28px;
            border-radius: 6px;
            background: #f0fdf4;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 24px;
            border-top: 1px solid #f1f5f9;
            background: #fafcff;
        }
        .exp-badge {
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 0.78rem;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
        }
        .days-text {
            font-size: 0.78rem;
            color: var(--gray);
        }

        /* ── EMPTY STATE ── */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: var(--gray);
        }
        .empty-state .empty-icon { font-size: 4rem; margin-bottom: 16px; }
        .empty-state h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            color: var(--navy);
            margin-bottom: 8px;
        }
        .empty-state a {
            display: inline-block;
            margin-top: 20px;
            background: var(--teal);
            color: white;
            padding: 10px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
        }

        /* ── FOOTER ── */
        footer {
            background: var(--navy);
            color: #64748b;
            text-align: center;
            padding: 28px;
            font-size: 0.85rem;
            margin-top: 40px;
        }
        footer span { color: var(--teal-light); }

        @media (max-width: 600px) {
            .stats-bar { gap: 24px; }
            .doctor-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<header>
    <div class="logo">Medi<span>Care</span></div>
    <nav>
        <a href="index.php" class="active">Home</a>
        <a href="admin.php" class="admin-btn">Admin Panel</a>
    </nav>
</header>

<!-- HERO -->
<section class="hero">
    <div class="hero-badge">Our Medical Team</div>
    <h1>Meet Our Expert Doctors</h1>
    <p>Browse our trusted team of medical professionals — experienced, available, and here for you.</p>
</section>

<!-- STATS -->
<?php
$total_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM doctors");
$total_row = mysqli_fetch_assoc($total_result);
$total_doctors = $total_row['total'];

$spec_result = mysqli_query($conn, "SELECT COUNT(DISTINCT specialization) as specs FROM doctors");
$spec_row = mysqli_fetch_assoc($spec_result);
$total_specs = $spec_row['specs'];

$exp_result = mysqli_query($conn, "SELECT AVG(experience) as avg_exp FROM doctors");
$exp_row = mysqli_fetch_assoc($exp_result);
$avg_exp = round($exp_row['avg_exp'] ?? 0);
?>
<div class="stats-bar">
    <div class="stat">
        <div class="stat-number"><?= $total_doctors ?></div>
        <div class="stat-label">Doctors</div>
    </div>
    <div class="stat">
        <div class="stat-number"><?= $total_specs ?></div>
        <div class="stat-label">Specializations</div>
    </div>
    <div class="stat">
        <div class="stat-number"><?= $avg_exp ?>+</div>
        <div class="stat-label">Avg. Yrs Experience</div>
    </div>
</div>

<!-- DOCTORS GRID -->
<main>
    <div class="section-header">
        <div class="section-title">
            <span>Available Now</span>
            All Doctors
        </div>
        <div class="count-badge"><?= $total_doctors ?> Total</div>
    </div>

    <?php
    $result = mysqli_query($conn, "SELECT * FROM doctors ORDER BY id DESC");
    if (mysqli_num_rows($result) > 0):
    ?>
    <div class="doctor-grid">
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="doctor-card">
            <div class="card-image-wrap">
                <?php if (!empty($row['image']) && file_exists('uploads/' . $row['image'])): ?>
                    <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                <?php else: ?>
                    <div class="card-image-placeholder">👨‍⚕️</div>
                <?php endif; ?>
                <div class="spec-tag"><?= htmlspecialchars($row['specialization']) ?></div>
            </div>

            <div class="card-body">
                <div class="doctor-name">Dr. <?= htmlspecialchars($row['name']) ?></div>
                <ul class="info-list">
                    <li class="info-item">
                        <div class="info-icon">📞</div>
                        <?= htmlspecialchars($row['phone']) ?>
                    </li>
                    <li class="info-item">
                        <div class="info-icon">✉️</div>
                        <?= htmlspecialchars($row['email']) ?>
                    </li>
                    <li class="info-item">
                        <div class="info-icon">📅</div>
                        <?= htmlspecialchars($row['available_days']) ?>
                    </li>
                </ul>
            </div>

            <div class="card-footer">
                <span class="exp-badge">⭐ <?= $row['experience'] ?> yrs exp</span>
                <span class="days-text">Available</span>
            </div>
        </div>
    <?php endwhile; ?>
    </div>

    <?php else: ?>
    <div class="empty-state">
        <div class="empty-icon">🏥</div>
        <h3>No Doctors Yet</h3>
        <p>Start by adding doctors from the Admin Panel.</p>
        <a href="admin.php">Go to Admin Panel</a>
    </div>
    <?php endif; ?>
</main>

<footer>
    &copy; <?= date('Y') ?> <span>MediCare</span> Doctor Management System &mdash; Built with PHP & MySQL
</footer>

</body>
</html>