INSERT OR IGNORE INTO users (name, email, gender, register_date, occupation_id)
VALUES
('Аксенов Роман Михайлович', 'aksenov@gmail.com', 'male', date('now'),
 (SELECT id FROM occupations ORDER BY id LIMIT 1)),
('Афонькин Дмитрий Евгеньевич', 'afonkin@gmail.com', 'male', date('now'),
 (SELECT id FROM occupations ORDER BY id LIMIT 1)),
('Гераськин Роман Геннадьевич', 'geraskin@gmail.com', 'male', date('now'),
 (SELECT id FROM occupations ORDER BY id LIMIT 1)),
('Доля Олег Альбертович', 'dolya@gmail.com', 'male', date('now'),
 (SELECT id FROM occupations ORDER BY id LIMIT 1)),
('Забненков Максим Алексеевич', 'zabnenkov@gmail.com', 'male', date('now'),
 (SELECT id FROM occupations ORDER BY id LIMIT 1));



INSERT OR IGNORE INTO movies (title, year)
VALUES
('Хороший, плохой, злой (1966)', 1966),
('Дюна (2021)', 2021),
('Индиана Джонс: В поисках утраченного ковчега (1981)', 1981);


INSERT OR IGNORE INTO genres (name) VALUES ('Western');
INSERT OR IGNORE INTO genres (name) VALUES ('Fantasy');
INSERT OR IGNORE INTO genres (name) VALUES ('Adventure');

INSERT OR IGNORE INTO movie_genres (movie_id, genre_id)
SELECT m.id, g.id FROM movies m JOIN genres g ON g.name = 'Western'
WHERE m.title = 'Хороший, плохой, злой (1966)';

INSERT OR IGNORE INTO movie_genres (movie_id, genre_id)
SELECT m.id, g.id FROM movies m JOIN genres g ON g.name = 'Fantasy'
WHERE m.title = 'Дюна (2021)';

INSERT OR IGNORE INTO movie_genres (movie_id, genre_id)
SELECT m.id, g.id FROM movies m JOIN genres g ON g.name = 'Adventure'
WHERE m.title = 'Индиана Джонс: В поисках утраченного ковчега (1981)';


INSERT INTO ratings (user_id, movie_id, rating, timestamp)
SELECT u.id, m.id, 4.9, strftime('%s','now')
FROM users u JOIN movies m ON m.title = 'Хороший, плохой, злой (1966)'
WHERE u.email = 'geraskin@gmail.com'
AND NOT EXISTS (
    SELECT 1 FROM ratings r WHERE r.user_id = u.id AND r.movie_id = m.id
);

INSERT INTO ratings (user_id, movie_id, rating, timestamp)
SELECT u.id, m.id, 5.0, strftime('%s','now')
FROM users u JOIN movies m ON m.title = 'Дюна (2021)'
WHERE u.email = 'geraskin@gmail.com'
AND NOT EXISTS (
    SELECT 1 FROM ratings r WHERE r.user_id = u.id AND r.movie_id = m.id
);

INSERT INTO ratings (user_id, movie_id, rating, timestamp)
SELECT u.id, m.id, 4.8, strftime('%s','now')
FROM users u JOIN movies m ON m.title = 'Индиана Джонс: В поисках утраченного ковчега (1981)'
WHERE u.email = 'geraskin@gmail.com'
AND NOT EXISTS (
    SELECT 1 FROM ratings r WHERE r.user_id = u.id AND r.movie_id = m.id
);