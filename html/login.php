<?php

/** 
 * HTML: login
 * This layout will be used if login page does not exists
 */

namespace ProcessWire;

$errors = [];

if ($input->post->submit) {

  $user_name = $sanitizer->text($input->post->user_name);
  $psw = $sanitizer->text($input->post->psw);

  if (empty($user_name)) $errors[] = __("Please enter user name or email");
  if (empty($psw)) $errors[] = __("Please enter your password");

  $usr = $users->get("name|email=$user_name");
  if ($usr == "") $errors[] = __("User not found");

  $u = null;

  try {
    $u = $session->login($usr->name, $psw);
    if ($u) {
      $session->redirect($config->urls->admin);
    } else {
      $errors[] = __("Password is incorrect");
    }
  } catch (WireException $e) {
    $errors[] = $e->getMessage();
  }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>

  <!-- UIkit CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.17.8/dist/css/uikit.min.css" />

  <!-- UIkit JS -->
  <script src="https://cdn.jsdelivr.net/npm/uikit@3.17.8/dist/js/uikit.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/uikit@3.17.8/dist/js/uikit-icons.min.js"></script>

  <script src="https://unpkg.com/htmx.org@1.9.8" integrity="sha384-rgjA7mptc2ETQqXoYC3/zJvkU7K/aP44Y+z7xQuJiVnB/422P/Ak+F/AqFR7E4Wr" crossorigin="anonymous"></script>

</head>

<body class="uk-background-muted uk-position-relative">

  <?php if (count($errors)) : ?>
    <div class="uk-position-top">
      <?php foreach ($errors as $error) : ?>
        <div class="uk-alert uk-alert-danger uk-margin-remove uk-text-center">
          <?= $error ?>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div class="uk-flex uk-flex-middle uk-flex-center uk-flex-column" uk-height-viewport>

    <h1>Login</h1>

    <div class="uk-card uk-card-default uk-card-body uk-border-rounded uk-width-large">
      <form id="login-form" action="./" method="POST" class="uk-form-stacked uk-margin-remove">

        <div class="uk-margin">
          <label class="uk-form-label" for="input-user_name">User Name</label>
          <input id="input-user_name" class="uk-input" type="text" name="user_name" placeholder="User name or email" />
        </div>

        <div class="uk-margin">
          <label class="uk-form-label" for="input-psw">Password</label>
          <input id="input-psw" class="uk-input" type="password" name="psw" placeholder="Password" />
        </div>

        <div class="uk-margin-top uk-position-relative">
          <input class="uk-button uk-button-primary uk-width-1-1" type="submit" name="submit" value="Login" />
        </div>

      </form>
    </div>

    <p class="uk-text uk-text-small uk-text-muted">
      <?= $system("site_name|app_name") ?>
    </p>

  </div>
</body>

</html>