<?php
include_once 'header.php';
 ?>
  <section class="main-container">
    <div class="main-wrapper" align="center">
      <h2>Signup</h2>
      <form class="signup-form" action="includes/signup.inc.php" method="POST">
        <input type="text" name="first" placeholder="First Name">
        <br>
        <input type="text" name="last" placeholder="Last Name">
        <br>
        <input type="text" name="email" placeholder="E-Mail">
        <br>
        <input type="text" name="uid" placeholder="Username">
        <br>
        <input type="password" name="pwd" placeholder="Password">
        <br>
        <button type="submit" name="submit">Register</button>


    </div>
  </section>

  <?php
  include_once 'footer.php';
   ?>
