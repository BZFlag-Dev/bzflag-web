# to set themes back to generic (to match cvs)
#     after importing foreign database

DELETE FROM bzl_themes;

INSERT INTO bzl_themes VALUES
 (1, 'dark', 'Dark Red', 'templates/genericdark'),
 (2, 'light', 'Lt. Blue', 'templates/genericlight');
