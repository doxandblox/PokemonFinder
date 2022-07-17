CREATE TABLE pokemon (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(64) NOT NULL UNIQUE,
    url VARCHAR(128),
    liked INTEGER DEFAULT 0,
    created_at datetime default current_timestamp
)