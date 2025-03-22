<?php
session_start();
require_once('token.php');
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <title>Chrono & Co.</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Roboto&display=swap"
      rel="stylesheet"
    />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link
      rel="stylesheet"
      href="styles/style.css"
      type="text/css"
      media="screen"
    />
    <link
      rel="stylesheet"
      href="styles/index.css"
      type="text/css"
      media="screen"
    />
    <link
      rel="stylesheet"
      href="styles/connexion.css"
      type="text/css"
      media="screen"
    />
    <link
      rel="stylesheet"
      href="styles/formulaire.css"
      type="text/css"
      media="screen"
    />
  </head>
  <body>
    <header>
      <h1>Chrono & Co.</h1>
    </header>
    <main>
    <form method="post">
      <input type="hidden" name="token" value="<?php echo generateToken(); ?>">
        <p>
            <label for="mail">Adresse e-mail :</label>
            <input type="email" id ="mail" name="mail" required autocomplete="username">
        </p>
        <p>
            <label for="mdp">Mot de passe :</label>
            <input type="password" id ="mdp" name="mdp" required autocomplete="current-password">
        </p>
        <p>
            <input type="submit" value="Se connecter">
        </p>
    </form>
    <div class="phrase_connexion">
        <p>Pas encore de compte ? <a href="nouveau.php">Créer un compte</a></p>
    </div>
    <a href="index.php" class="retour_connexion">Retour</a>
    </main>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="connexion.js"></script>
  </body>
</html>