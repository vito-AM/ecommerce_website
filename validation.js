$(document).ready(function () {
  const form = $("form");
  const submitBtn = $('input[type="submit"]');
  let isValid = {
    n: false,
    p: false,
    adr: false,
    num: false,
    mail: false,
    mdp1: false,
    mdp2: false,
  };

  // Ajout des messages de validation
  $("input").each(function () {
    $(this).after('<div class="validation-message"></div>');
  });

  function validateField(field, value) {
    const msgElement = field.siblings(".validation-message");

    if (!value) {
      field.addClass("error").removeClass("success");
      msgElement
        .text("Ce champ est obligatoire")
        .addClass("error-text")
        .removeClass("success-text");
      return false;
    }

    switch (field.attr("id")) {
      case "mail":
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
          field.addClass("error").removeClass("success");
          msgElement
            .text("Format email invalide")
            .addClass("error-text")
            .removeClass("success-text");
          return false;
        }
        $.post("verification_mail.php", { mail: value }, function (response) {
          if (response.exists) {
            field.addClass("error").removeClass("success");
            msgElement
              .text("Cette adresse email existe déjà")
              .addClass("error-text")
              .removeClass("success-text");
            isValid.mail = false;
          } else {
            field.removeClass("error").addClass("success");
            msgElement
              .text("Valide")
              .removeClass("error-text")
              .addClass("success-text");
            isValid.mail = true;
          }
          submitBtn.prop("disabled", !Object.values(isValid).every(Boolean));
        });
        return true;

      case "mdp1":
        const pwdRegex =
          /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{1,}$/;
        if (!pwdRegex.test(value)) {
          field.addClass("error").removeClass("success");
          msgElement
            .text(
              "Le mot de passe doit contenir au moins 1 lettre, 1 chiffre et 1 caractère spécial"
            )
            .addClass("error-text")
            .removeClass("success-text");
          return false;
        }
        if ($("#mdp2").val()) {
          validateField($("#mdp2"), $("#mdp2").val());
        }
        break;

      case "mdp2":
        if (value !== $("#mdp1").val()) {
          field.addClass("error").removeClass("success");
          msgElement
            .text("Les mots de passe ne correspondent pas")
            .addClass("error-text")
            .removeClass("success-text");
          return false;
        }
        break;
    }

    field.removeClass("error").addClass("success");
    msgElement
      .text("Valide")
      .removeClass("error-text")
      .addClass("success-text");
    return true;
  }

  $("input").on("blur", function () {
    const field = $(this);
    isValid[field.attr("id")] = validateField(field, field.val());
    submitBtn.prop("disabled", !Object.values(isValid).every(Boolean));
  });

  function showMessage(message, type) {
    // Supprimer l'ancien message s'il existe
    $(".message-container").remove();

    // Créer et insérer le nouveau message
    const messageClass =
      type === "success" ? "success-message" : "error-message";
    const messageHtml = `<div class="message-container"><p class="${messageClass}">${message}</p></div>`;
    $("main").prepend(messageHtml);
  }

  form.on("submit", function (e) {
    e.preventDefault();

    if (!Object.values(isValid).every(Boolean)) {
      return;
    }

    const formData = $(this).serialize();

    $.ajax({
      url: "enregistrement.php",
      type: "POST",
      data: formData,
      dataType: "json",
      success: function (response) {
        console.log(response); // Ajoutons ceci pour déboguer
        if (response.success) {
          showMessage("Compte créé avec succès! Redirection...", "success");
          setTimeout(() => {
            window.location.href = "index.php";
          }, 1000);
        } else {
          showMessage(response.message, "error");
        }
      },
      error: function (xhr, status, error) {
        console.log(xhr.responseText); // Ajoutons ceci pour déboguer
        showMessage("Erreur lors de la création du compte", "error");
      },
    });
  });
});
