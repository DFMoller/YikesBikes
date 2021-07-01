<?php



?>

<nav>
  <div class="nav-wrapper">

    <div class="nav-left">
      <h3><a href="redirect.php?destination=home.php"><i>YikesBikes</i></a></h3>
      <ul class="nav-links">
        <li><a href="redirect.php?destination=products.php">Bikes</a></li>
        <li><a href="#">Gallery</a></li>
        <li><a href="#">Contact</a></li>
      </ul>
    </div>

    <div class="nav-right">
      <?php if (isset($_SESSION['username'])) : ?>
        <!-- <div class="cart-link">
          <svg class="cart" width="77" height="55" viewBox="0 0 77 55" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path class="wheel" d="M53.3239 49.5C53.3239 47.2674 55.0077 46.1512 56.662 46.1512C58.3162 46.1512 60 47.2674 60 49.5C60 51.7326 58.4137 52.8488 56.662 52.8488C54.9102 52.8488 53.3239 51.7326 53.3239 49.5Z" fill="black"/>
            <path class="wheel" d="M27.5 49.5C27.5 47.2674 29.1838 46.1512 30.838 46.1512C32.4923 46.1512 34.1761 47.2674 34.1761 49.5C34.1761 51.7326 32.5898 52.8488 30.838 52.8488C29.0863 52.8488 27.5 51.7326 27.5 49.5Z" fill="black"/>
            <path d="M19.3169 2.47675L28.6972 31.5H46.5H64.3028L74.3169 2.47674M2 2.47675H11L22.9296 38.8372H64.3028M56.662 46.1512C55.0077 46.1512 53.3239 47.2674 53.3239 49.5C53.3239 51.7326 54.9102 52.8488 56.662 52.8488C58.4137 52.8488 60 51.7326 60 49.5C60 47.2674 58.3162 46.1512 56.662 46.1512ZM30.838 46.1512C29.1838 46.1512 27.5 47.2674 27.5 49.5C27.5 51.7326 29.0863 52.8488 30.838 52.8488C32.5898 52.8488 34.1761 51.7326 34.1761 49.5C34.1761 47.2674 32.4923 46.1512 30.838 46.1512Z" stroke="black" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <p class="p2">CART</p>
        </div> -->
        <a class="nav-cart-link" href="redirect.php?destination=cart.php">
          <svg class="nav-cart" width="71" height="57" viewBox="0 0 71 57" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path class="nav-cart-wheel" d="M52.6851 49.5C52.6851 47.4592 54.3688 46.4388 56.0231 46.4388C57.6774 46.4388 59.3611 47.4592 59.3611 49.5C59.3611 51.5408 57.7749 52.5612 56.0231 52.5612C54.2713 52.5612 52.6851 51.5408 52.6851 49.5Z" fill="black"/>
          <path class="nav-cart-wheel" d="M24.2907 49.5C24.2907 47.4592 25.9745 46.4388 27.6287 46.4388C29.283 46.4388 30.9667 47.4592 30.9667 49.5C30.9667 51.5408 29.3805 52.5612 27.6287 52.5612C25.877 52.5612 24.2907 51.5408 24.2907 49.5Z" fill="black"/>
          <path d="M4 4H13L16.642 14M16.642 14L24.2907 35.0017H59.3611L66.3611 14H16.642ZM56.0231 46.4388C54.3688 46.4388 52.6851 47.4592 52.6851 49.5C52.6851 51.5408 54.2713 52.5612 56.0231 52.5612C57.7749 52.5612 59.3611 51.5408 59.3611 49.5C59.3611 47.4592 57.6774 46.4388 56.0231 46.4388ZM27.6287 46.4388C25.9745 46.4388 24.2907 47.4592 24.2907 49.5C24.2907 51.5408 25.877 52.5612 27.6287 52.5612C29.3805 52.5612 30.9667 51.5408 30.9667 49.5C30.9667 47.4592 29.283 46.4388 27.6287 46.4388Z" stroke="black" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </a>
        
        <div class="dropdown">
          <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
            <?php echo $_SESSION['username']; ?>
          </button>
          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
            <li><a class="dropdown-item" href="redirect.php?destination=account.php">My Account</a></li>
            <li><a class="dropdown-item" href="redirect.php?destination=cart.php">My Shopping Cart</a></li>
            <li><a class="dropdown-item" href="#">Purchase History</a></li>
            <li><a class="dropdown-item" href="redirect.php?destination=logout.php">Log Out</a></li>
          </ul>
        </div>
      <?php else : ?>
        <a href="login.php">Log In</a>
      <?php endif ?>

    </div>

  </div>
</nav>