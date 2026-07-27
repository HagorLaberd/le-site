<?php
session_start();

// Mot de passe (change-le !)
$mot_de_passe_admin = "Monamour.leticiaM";

require_once '../db_connect.php';

// Connexion
if (isset($_POST['password'])) {
    if ($_POST['password'] === $mot_de_passe_admin) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $error = "Mot de passe incorrect";
    }
}

// Déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Vérifier si connecté
$is_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// ACTIONS (si connecté)
if ($is_logged_in) {
    
    // Supprimer
    if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
        $id = $_GET['delete'];
        $pdo->prepare("DELETE FROM messages WHERE id = ?")->execute([$id]);
        header('Location: admin.php');
        exit;
    }
    
    // Marquer lu/non lu
    if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
        $id = $_GET['toggle'];
        $stmt = $pdo->prepare("SELECT lu FROM messages WHERE id = ?");
        $stmt->execute([$id]);
        $msg = $stmt->fetch();
        if ($msg) {
            $nouvel_etat = $msg['lu'] ? 0 : 1;
            $pdo->prepare("UPDATE messages SET lu = ? WHERE id = ?")->execute([$nouvel_etat, $id]);
        }
        header('Location: admin.php');
        exit;
    }
}

// Si PAS connecté : formulaire
if (!$is_logged_in):
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin</title>


   <style>
    body {
        font-family: Arial;
        background: #f4f7fb;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
    .login-box {
        background: white;
        padding: 40px;
        border-radius: 8px;
        width: 320px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    h2 {
        color: #00558c;
        text-align: center;
        margin-top: 0;
        margin-bottom: 25px;
    }
    input {
        width: 100%;
        padding: 12px;
        margin: 10px 0 20px 0;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
        box-sizing: border-box;
    }
    button {
        width: 100%;
        padding: 12px;
        background: #00558c;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        box-sizing: border-box;
    }
    button:hover {
        background: #003d66;
    }
    .error {
        color: #dc3545;
        text-align: center;
        margin: 10px 0;
        font-size: 14px;
    }
    /* ===== RESPONSIVE ADMIN ===== */
@media screen and (max-width: 768px) {
    .stats {
        flex-direction: column;
        gap: 15px;
    }
    
    .entete {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .date {
        margin-top: 5px;
    }
    
    .actions {
        flex-direction: column;
        width: 100%;
    }
    
    .btn {
        width: 100%;
        text-align: center;
        margin: 2px 0;
    }
    
    .filters {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    
    .filters a {
        margin-right: 0;
        text-align: center;
    }
}
</style>


</head>
<body>
    <div class="login-box">
        <h2>🔐 Administration</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>
</html>
<?php
    exit;
endif;

// ============================================
// PAGE D'ADMIN (connecté)
// ============================================

// Récupérer tous les messages
$messages = $pdo->query("SELECT * FROM messages ORDER BY date_envoi DESC")->fetchAll();

// Compter les non lus
$non_lus = 0;
foreach ($messages as $m) {
    if ($m['lu'] == 0) $non_lus++;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Messages</title>
    <style>
        body { font-family: Arial; background: #f4f7fb; padding: 30px; }
        .container { max-width: 1000px; margin: 0 auto; }
        
        h1 { color: #00558c; display: flex; justify-content: space-between; align-items: center; }
        .badge { background: #dc3545; color: white; padding: 5px 15px; border-radius: 20px; font-size: 16px; }
        
        .stats { background: white; padding: 20px; border-radius: 8px; margin-bottom: 30px; display: flex; gap: 30px; }
        .stat-item { flex: 1; text-align: center; }
        .stat-number { font-size: 36px; font-weight: bold; color: #00558c; }
        .stat-label { color: #666; font-size: 14px; }
        
        .filters { margin: 20px 0; }
        .filters a { padding: 8px 20px; border: 1px solid #00558c; background: white; color: #00558c; text-decoration: none; border-radius: 5px; margin-right: 10px; }
        .filters a.active { background: #00558c; color: white; }
        
        .message {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #00558c;
            width: 100%;           /* ← important */
            clear: both;           /* ← pour éviter les chevauchements */
        }
        
        .message.nonlu {
            background: #fff9e6;
            border-left: 4px solid #ffc107;
            position: relative;
        }
        
        .message.nonlu::after {
            content: "NON LU";
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ffc107;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 12px;
        }
        
        .entete {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            width: 100%;
        }
        
        .nom {
            font-weight: bold;
            color: #00558c;
            font-size: 18px;
        }
        
        .email {
            color: #666;
        }
        
        .date {
            color: #999;
            font-size: 13px;
        }
        
        .details {
            color: #666;
            margin: 10px 0;
            font-size: 14px;
        }
        
        .contenu {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            line-height: 1.6;
        }
        
        .actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            border-top: 1px solid #eee;
            padding-top: 15px;
            margin-top: 10px;
            flex-wrap: wrap;        /* ← pour mobile */
        }
        
        .btn {
            padding: 8px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            display: inline-block;
        }
        
        .btn-lu { background: #ffc107; color: #333; }
        .btn-repondre { background: transparent; border: 1px solid #00558c; color: #00558c; }
        .btn-supprimer { background: #dc3545; color: white; }
        
        .logout {
            background: #6c757d;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
        }
        
        .vide {
            text-align: center;
            padding: 60px;
            background: white;
            border-radius: 8px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1>
                📬 Messages reçus
                <?php if ($non_lus > 0): ?>
                    <span class="badge"><?= $non_lus ?> non lu<?= $non_lus > 1 ? 's' : '' ?></span>
                <?php endif; ?>
            </h1>
            <a href="?logout=1" class="logout">Déconnexion</a>
        </div>
        
        <div class="stats">
            <div class="stat-item">
                <div class="stat-number"><?= count($messages) ?></div>
                <div class="stat-label">Total messages</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= $non_lus ?></div>
                <div class="stat-label">Non lus</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= date('d/m/Y') ?></div>
                <div class="stat-label">Aujourd'hui</div>
            </div>
        </div>
        
        <div class="filters">
            <a href="?filter=all" class="<?= !isset($_GET['filter']) || $_GET['filter'] == 'all' ? 'active' : '' ?>">Tous</a>
            <a href="?filter=unread" class="<?= isset($_GET['filter']) && $_GET['filter'] == 'unread' ? 'active' : '' ?>">Non lus</a>
        </div>
        
        <?php
        $filtre = $_GET['filter'] ?? 'all';
        $liste = $messages;
        if ($filtre == 'unread') {
            $liste = array_filter($messages, function($m) { return $m['lu'] == 0; });
        }
        
        if (empty($liste)): ?>
            <div class="vide">📭 Aucun message à afficher</div>
        <?php else: ?>
            <?php foreach ($liste as $msg): ?>
                <div class="message <?= $msg['lu'] ? '' : 'nonlu' ?>">
                    
                    <div class="entete">
                        <div>
                            <span class="nom"><?= htmlspecialchars($msg['nom']) ?></span>
                            <span class="email"> - <?= htmlspecialchars($msg['email']) ?></span>
                        </div>
                        <div class="date"><?= date('d/m/Y H:i', strtotime($msg['date_envoi'])) ?></div>
                    </div>
                    
                    <?php if (!empty($msg['telephone']) || !empty($msg['source'])): ?>
                        <div class="details">
                            <?php if (!empty($msg['telephone'])): ?>📞 <?= htmlspecialchars($msg['telephone']) ?><?php endif; ?>
                            <?php if (!empty($msg['source'])): ?> - 🔍 Source : <?= htmlspecialchars($msg['source']) ?><?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="contenu"><?= nl2br(htmlspecialchars($msg['message'])) ?></div>
                    
                   <div class="actions">
                        <a href="?toggle=<?= $msg['id'] ?>" class="btn btn-lu">
                          <?= $msg['lu'] ? '🔴 Marquer non lu' : '✅ Marquer lu' ?>
                    </a>
                        <a href="mailto:<?= htmlspecialchars($msg['email']) ?>?subject=Réponse à votre message" class="btn btn-repondre">
                            ✉️ Répondre
                        </a>
                        <a href="?delete=<?= $msg['id'] ?>" class="btn btn-supprimer" onclick="return confirm('Supprimer ce message ?')">
                            🗑️ Supprimer
                        </a>
                    </div>
                    
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="contact-us.html" style="color: #00558c;">⬅ Retour formulaire</a>
        </div>
        
    </div>
   
</body>
</html>