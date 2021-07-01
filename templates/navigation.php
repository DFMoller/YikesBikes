<?php



?>

<nav>
  <div class="nav-wrapper">

    <div class="nav-left">
      <h3><a class="website-title-link" href="redirect.php?destination=home.php"><i class="website-title">YikesBikes</i></a></h3>
    </div>

    <div class="nav-right">

      <a class="nav-bikes-link" href="redirect.php?destination=products.php">        
        <svg class="bikes-icon" width="99" height="59" viewBox="0 0 99 59" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M44.3047 39.1622H19.8125L37.625 14.6351M44.3047 39.1622L37.625 14.6351M44.3047 39.1622L70.3703 14.6351M79.1875 39.1622L70.3703 14.6351M35.3984 6.45946L37.625 14.6351M37.625 14.6351H70.3703M70.3703 14.6351L65.8281 2H76.2188M30.2031 6.45946H41.3359M37.625 39.1622C37.625 49.0137 29.6501 57 19.8125 57C9.97493 57 2 49.0137 2 39.1622C2 29.3106 9.97493 21.3243 19.8125 21.3243C29.6501 21.3243 37.625 29.3106 37.625 39.1622ZM97 39.1622C97 49.0137 89.0251 57 79.1875 57C69.3499 57 61.375 49.0137 61.375 39.1622C61.375 29.3106 69.3499 21.3243 79.1875 21.3243C89.0251 21.3243 97 29.3106 97 39.1622Z" stroke="black" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </a>

      <?php if (isset($_SESSION['username'])) : ?>

        <a class="nav-cart-link" href="redirect.php?destination=cart.php">
          <svg class="nav-cart" width="71" height="57" viewBox="0 0 71 57" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path class="nav-cart-wheel" d="M52.6851 49.5C52.6851 47.4592 54.3688 46.4388 56.0231 46.4388C57.6774 46.4388 59.3611 47.4592 59.3611 49.5C59.3611 51.5408 57.7749 52.5612 56.0231 52.5612C54.2713 52.5612 52.6851 51.5408 52.6851 49.5Z" fill="black"/>
          <path class="nav-cart-wheel" d="M24.2907 49.5C24.2907 47.4592 25.9745 46.4388 27.6287 46.4388C29.283 46.4388 30.9667 47.4592 30.9667 49.5C30.9667 51.5408 29.3805 52.5612 27.6287 52.5612C25.877 52.5612 24.2907 51.5408 24.2907 49.5Z" fill="black"/>
          <path d="M4 4H13L16.642 14M16.642 14L24.2907 35.0017H59.3611L66.3611 14H16.642ZM56.0231 46.4388C54.3688 46.4388 52.6851 47.4592 52.6851 49.5C52.6851 51.5408 54.2713 52.5612 56.0231 52.5612C57.7749 52.5612 59.3611 51.5408 59.3611 49.5C59.3611 47.4592 57.6774 46.4388 56.0231 46.4388ZM27.6287 46.4388C25.9745 46.4388 24.2907 47.4592 24.2907 49.5C24.2907 51.5408 25.877 52.5612 27.6287 52.5612C29.3805 52.5612 30.9667 51.5408 30.9667 49.5C30.9667 47.4592 29.283 46.4388 27.6287 46.4388Z" stroke="black" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </a>
        
        <div class="dropdown">
          <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
            <?php echo $_SESSION['username']; ?>
          </button>
          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
            <li><a class="dropdown-item" href="redirect.php?destination=account.php">My Account</a></li>
            <li><a class="dropdown-item" href="redirect.php?destination=logout.php">Log Out</a></li>
          </ul>
        </div>
      <?php else : ?>
        <a href="login.php">Log In</a>
      <?php endif ?>

    </div>

  </div>
</nav>