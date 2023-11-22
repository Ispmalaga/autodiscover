CREATE TABLE email_configurations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    domain VARCHAR(255) NOT NULL,
    imap_server VARCHAR(255),
    smtp_server VARCHAR(255),
    imap_port INT,
    smtp_port INT,
    ssl BOOLEAN,
    username VARCHAR(255),
    password VARCHAR(255)
);
