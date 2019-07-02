<h1 style="text-align: center;margin-top: 20px;margin-bottom: 20px">This is the test page for replace with bank page</h1>
<hr>
<h3>price : <?php echo $_POST['price'] ; ?> - TransID Code : <?php echo $rand = rand(0 , 99999);  ?></h3>
<a style="color: #00219d;margin-top: 20px;margin-bottom: 20px" href="<?php echo $_POST['CallbackURL'] ; ?>?&Status=OK&Authority=<?php echo $rand ; ?>" >pay it</a><br><br>
<a style="color: #f40004;margin-top: 20px;margin-bottom: 20px" href="<?php echo $_POST['CallbackURL'] ; ?>?&Status=NOK&Authority=<?php echo $rand ; ?>" >dont Pay It</a><br>