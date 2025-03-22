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
      href="styles/nouveau.css"
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
      <?php
      if (isset($_GET['message'])) {
        echo "<div class='message-container'>
        <p class='error-message'>" . $_GET['message'] . "</p>
        </div>";
      }
      ?>
    <form method="post">
        <p>
            <label for="n">Nom :</label>
            <input type="texte" id ="n" name="n" required>
        </p>
        <p>
            <label for="p">Prénom :</label>
            <input type="texte" id ="p" name="p" required>
        </p>
        <p>
            <label for="adr">Adresse :</label>
            <input type="texte" id ="adr" name="adr" required>
        </p>
        <p>
            <label for="num">Numéro de téléphone :</label>
            <input type="tel" id ="num" name="num" required>
        </p>
        <p>
            <label for="mail">Adresse e-mail :</label>
            <input type="email" id ="mail" name="mail" required autocomplete="username" >
        </p>
        <p>
            <label for="mdp1">Mot de passe :</label>
            <input type="password" id ="mdp1" name="mdp1" required autocomplete="new-password">
        </p>
        <p>
            <label for="mdp2">Confirmer votre mot de passe :</label>
            <input type="password" id ="mdp2" name="mdp2" required autocomplete="new-password">
        </p>
        <p>
            <input type="submit" value="Envoyer">
        </p>
    </form>
    <a href="index.php" class="retour">Retour</a>
    </main>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="validation.js"></script>
  </body>
</html>