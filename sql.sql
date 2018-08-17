ALTER USER 'guest'@'%' IDENTIFIED BY '123456';

GRANT Alter, Alter Routine, Create, Create Routine, Create Temporary Tables, Create View, Delete, Drop, Event, Execute, Grant Option, Index, Insert, Lock Tables, References, Select, Show View, Trigger, Update ON 'gps'.* TO 'guest'@'%';

REVOKE Alter, Alter Routine, Create, Create Routine, Create Temporary Tables, Create View, Delete, Drop, Event, Execute, Index, Insert, Lock Tables, References, Select, Show View, Trigger, Update ON 'idcard'.* FROM 'guest'@'%';