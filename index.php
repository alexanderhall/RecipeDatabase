<html>
  <head>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <div class="center">
    <form action="checking.php" method="post" id="recipeform">
      <input type="text" name="username" placeholder="Username" value="testuser">
      <input type="password" name="pword" placeholder="Password">
      <input type="text" name="database" placeholder="Database" value="Cookbook">
      <input type="text" name="recipename" placeholder="Recipe Name">
      <input type="text" name="site" placeholder="Website">
    </form>
    <textarea name="ingredients" rows="5" placeholder="Ingredients (separated by comma or new line)&#10;3 cups flour&#10;2 eggs&#10;..." form="recipeform"></textarea>
    <textarea name="directions" rows="10" placeholder="Recipe Directions" form="recipeform"></textarea>
    <input type="submit" form="recipeform" name='submit'>
    </div>
  </body>
</html>
