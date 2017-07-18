# Found this at https://gist.github.com/greghelton/1546514
# Modified heavily but it was a good starting point.
#-- start the server: $ mysqld --console
#-- login:            $ mysql -u root --password=wxyz
#-- run the script:   mysql> source /Users/javapro/dev/src/sql/Cookbook.sql
#-- the script: 

drop database if exists Cookbook;

create database Cookbook; 

connect Cookbook; 
	
create table Recipe (id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
	name VARCHAR(200), 
	website VARCHAR(500), 
	instructions VARCHAR(1500)) 
	ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table Ingredient (id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
	name VARCHAR(50)) 
	ENGINE=InnoDB DEFAULT CHARSET=utf8; 

create table RecipeIngredient (recipe_id INT NOT NULL, 
	ingredient_id INT NOT NULL, 
	measurement VARCHAR(50),	
	CONSTRAINT fk_recipe FOREIGN KEY(recipe_id) REFERENCES Recipe(id), 
	CONSTRAINT fk_ingredient FOREIGN KEY(ingredient_id) REFERENCES Ingredient(id)) 
	ENGINE=InnoDB DEFAULT CHARSET=utf8; 

#INSERT INTO Recipe (name, instructions) VALUES('Chocolate Cake', 'Add eggs, flour, chocolate to pan. Bake at 350 for 1 hour');
#INSERT INTO Ingredient (name) VALUES('egg'), ('salt'), ('sugar'), ('chocolate'), ('vanilla extract'), ('flour');
#INSERT INTO RecipeIngredient (recipe_id, ingredient_id, measurement)  VALUES (1, 1, '3');
#INSERT INTO RecipeIngredient (recipe_id, ingredient_id, measurement)  VALUES (1, 2, '1 tsps');
#INSERT INTO RecipeIngredient (recipe_id, ingredient_id, measurement)  VALUES (1, 3, '2 cups');
#INSERT INTO RecipeIngredient (recipe_id, ingredient_id, measurement)  VALUES (1, 4, '1 cups');

#INSERT INTO Recipe (name) VALUES ('Lemon Cake');
#INSERT INTO Ingredient (name) VALUES ('lemons');

#INSERT INTO RecipeIngredient (recipe_id, ingredient_id, measurement) 
#SELECT r.id, i.id, '3'
#FROM Recipe r
#JOIN Ingredient i
#WHERE r.name = 'Lemon Cake' AND i.name = "lemons";

SELECT r.name AS 'Recipe', i.name AS 'Ingredient', ri.measurement AS 'Measure'
FROM Recipe r 
JOIN RecipeIngredient ri on r.id = ri.recipe_id 
JOIN Ingredient i on i.id = ri.ingredient_id
ORDER BY r.name;
