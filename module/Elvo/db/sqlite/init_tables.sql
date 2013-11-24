
CREATE TABLE vote (
  id INTEGER PRIMARY KEY,
  data TEXT NOT NULL,
  key TEXT NOT NULL
);

CREATE TABLE voter (
  voter_id VARCHAR(64) PRIMARY KEY
);

CREATE TABLE votetime (
  votetime DATETIME
);

