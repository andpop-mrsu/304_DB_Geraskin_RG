#!/bin/bash
chcp 65001

sqlite3 movies_rating.db < db_init.sql

echo "1.  Составить список фильмов, имеющих хотя бы одну оценку. Список фильмов отсортировать по году выпуска и по названиям. В списке оставить первые 10 фильмов."
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "SELECT movies.title, movies.year 
FROM movies
JOIN ratings ON movies.id = ratings.movie_id
ORDER BY movies.year, movies.title
LIMIT 10;"
echo " "

echo "2.  Вывести список всех пользователей, фамилии (не имена!) которых начинаются на букву 'A'. Полученный список отсортировать по дате регистрации. В списке оставить первых 5 пользователей."
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "SELECT users.name, users.register_date
FROM users
WHERE name LIKE '% A%'
ORDER BY register_date
LIMIT 5;"
echo " "

echo "3.  Написать запрос, возвращающий информацию о рейтингах в более читаемом формате: имя и фамилия эксперта, название фильма, год выпуска, оценка и дата оценки в формате ГГГГ-ММ-ДД. Отсортировать данные по имени эксперта, затем названию фильма и оценке. В списке оставить первые 50 записей."
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "SELECT users.name, movies.title, movies.year, ratings.rating,  DATE(datetime(ratings.timestamp, 'unixepoch'))
FROM ratings
JOIN movies ON movies.id=ratings.movie_id
JOIN users ON users.id=ratings.user_id
ORDER BY users.name, movies.title, ratings.rating
LIMIT 50;"
echo " "

echo "4. Вывести список фильмов с указанием тегов, которые были им присвоены пользователями. Сортировать по году выпуска, затем по названию фильма, затем по тегу. В списке оставить первые 40 записей."
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "SELECT movies.title, tags.tag
FROM movies
JOIN tags ON movies.id = tags.movie_id
ORDER BY movies.year, movies.title, tags.tag
LIMIT 40;"
echo " "

echo "5. Вывести список самых свежих фильмов. В список должны войти все фильмы последнего года выпуска, имеющиеся в базе данных. Запрос должен быть универсальным, не зависящим от исходных данных (нужный год выпуска должен определяться в запросе, а не жестко задаваться)."
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "SELECT title, year
FROM movies
WHERE year=(
	SELECT MAX(year) 
	FROM movies
);"
echo " "

echo "6. Найти все драмы, выпущенные после 2005 года, которые понравились женщинам (оценка не ниже 4.5). Для каждого фильма в этом списке вывести название, год выпуска и количество таких оценок. Результат отсортировать по году выпуска и названию фильма."
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "SELECT movies.title, movies.year, COUNT(ratings.rating)
FROM movies
JOIN ratings ON movies.id=ratings.movie_id
JOIN users ON users.id=ratings.user_id
WHERE movies.genres LIKE '%Drama%' 
	AND movies.year>2005 
	AND users.gender='female'
	AND ratings.rating>=4.5
GROUP BY  movies.title, movies.year
ORDER BY movies.year, movies.title;"
echo " "

echo "Провести анализ востребованности ресурса - вывести количество пользователей, регистрировавшихся на сайте в каждом году. Найти, в каких годах регистрировалось больше всего и меньше всего пользователей."
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "SELECT COUNT(id), substr(register_date, 1, 4)
FROM users
GROUP BY substr(register_date, 1, 4)
ORDER BY substr(register_date, 1, 4);"
sqlite3 movies_rating.db -box -echo "SELECT COUNT(id), substr(register_date, 1, 4)
FROM users
GROUP BY substr(register_date, 1, 4)
ORDER BY COUNT(id) DESC 
LIMIT 1;"
sqlite3 movies_rating.db -box -echo "SELECT COUNT(id), substr(register_date, 1, 4)
FROM users
GROUP BY substr(register_date, 1, 4)
ORDER BY COUNT(id)
LIMIT 1;"
echo " "




