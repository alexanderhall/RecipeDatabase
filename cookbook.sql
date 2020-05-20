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
	name VARCHAR(100)) 
	ENGINE=InnoDB DEFAULT CHARSET=utf8; 

create table Measure (id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	name VARCHAR(30))
	ENGINE=InnoDB DEFAULT CHARSET=utf8; 

create table RecipeIngredient (recipe_id INT NOT NULL, 
	ingredient_id INT NOT NULL, 
	measure_id INT,
        amount DECIMAL(10,5),
	CONSTRAINT fk_recipe FOREIGN KEY(recipe_id) REFERENCES Recipe(id), 
	CONSTRAINT fk_ingredient FOREIGN KEY(ingredient_id) REFERENCES Ingredient(id),
	CONSTRAINT fk_measure FOREIGN KEY (measure_id) REFERENCES Measure(id)) 
	ENGINE=InnoDB DEFAULT CHARSET=utf8; 
/*
INSERT INTO Recipe (name) VALUES ('Lemon Cake');
INSERT INTO Ingredient (name) VALUES ('lemons');
INSERT INTO RecipeIngredient (recipe_id, ingredient_id, measure_id, amount) 
SELECT r.id, i.id, NULL, '3'
FROM Recipe r
JOIN Ingredient i
WHERE r.name = 'Lemon Cake' AND i.name = "lemons";

INSERT INTO Ingredient (name) VALUES ('flour');
INSERT INTO Measure (name) VALUES ('cups');
INSERT INTO RecipeIngredient (recipe_id, ingredient_id, measure_id, amount) 
SELECT r.id, i.id, m.id, '2'
FROM Recipe r
JOIN Ingredient i
JOIN Measure m
WHERE r.name = 'Lemon Cake' AND i.name = "flour" AND m.name="cups";
*/
-- Initialize with scrambled egg recipe.
/*INSERT INTO Recipe (name, instructions) 
VALUES ('INIT Scrambled Eggs','Beat eggs together.Cook on low heat until done.');
INSERT INTO Ingredient (name) VALUES ('eggs');
INSERT INTO RecipeIngredient (recipe_id, ingredient_id, measure_id, amount) 
SELECT r.id, i.id, NULL, '3'
FROM Recipe r
JOIN Ingredient i
WHERE r.name = 'INIT Scrambled Eggs' AND i.name = "eggs";

INSERT INTO Ingredient (name) VALUES ('salt');
INSERT INTO Measure (name) VALUES ('tsp');
INSERT INTO RecipeIngredient (recipe_id, ingredient_id, measure_id, amount) 
SELECT r.id, i.id, m.id, '0.125'
FROM Recipe r
JOIN Ingredient i
JOIN Measure m
WHERE r.name = 'INIT Scrambled Eggs' AND i.name = 'salt' AND m.name='tsp';
*/
SELECT r.name AS 'Recipe', 
ri.amount AS 'Amount', 
m.name AS 'Measure', 
i.name AS 'Ingredient'
FROM Recipe r 
JOIN RecipeIngredient ri on r.id = ri.recipe_id 
JOIN Ingredient i on i.id = ri.ingredient_id
LEFT OUTER JOIN Measure m on m.id = ri.measure_id
ORDER BY r.name;
