-- SQL schema for the soccer results database
CREATE DATABASE IF NOT EXISTS soccer;

USE soccer;

CREATE TABLE IF NOT EXISTS results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    home_team VARCHAR(255) NOT NULL,
    away_team VARCHAR(255) NOT NULL,
    home_goals INT NOT NULL,
    away_goals INT NOT NULL,
    matchday INT NOT NULL,
    league_id INT NOT NULL,
    FOREIGN KEY (league_id) REFERENCES leagues(id)
);

CREATE TABLE IF NOT EXISTS leagues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    country VARCHAR(255) NOT NULL,
    tier INT NOT NULL
);

CREATE TABLE IF NOT EXISTS teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    league_id INT NOT NULL,
    FOREIGN KEY (league_id) REFERENCES leagues(id)
);

-- Insert initial leagues
INSERT INTO leagues (name, country, tier) VALUES 
('Premier League', 'England', 1),
('Championship', 'England', 2),
('League One', 'England', 3),
('League Two', 'England', 4),
('National League', 'England', 5),
('Bundesliga', 'Germany', 1),
('2. Bundesliga', 'Germany', 2),
('Superliga', 'Denmark', 1),
('Allsvenskan', 'Sweden', 1),
('Eliteserien', 'Norway', 1);

-- Insert initial teams (example data, replace with actual teams)
INSERT INTO teams (name, league_id) VALUES 
('Manchester United', 1),
('Liverpool', 1),
('Leeds United', 2),
('Norwich City', 2),
('Sunderland', 3),
('Portsmouth', 3),
('Exeter City', 4),
('Leyton Orient', 4),
('Wrexham', 5),
('Aldershot Town', 5),
('Bayern Munich', 6),
('Borussia Dortmund', 6),
('Hamburger SV', 7),
('Hannover 96', 7),
('FC Copenhagen', 8),
('Brøndby IF', 8),
('AIK', 9),
('Malmö FF', 9),
('Rosenborg', 10),
('Molde', 10);
