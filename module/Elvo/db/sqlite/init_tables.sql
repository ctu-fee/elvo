
CREATE TABLE IF NOT EXISTS vote (
  id INTEGER PRIMARY KEY,
  data TEXT NOT NULL,
  key TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS voter (
  voter_id VARCHAR(64) PRIMARY KEY
);

CREATE TABLE IF NOT EXISTS votetime (
  votetime DATETIME
);

