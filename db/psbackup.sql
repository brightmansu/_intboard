CREATE OR REPLACE FUNCTION show_ddl_table (table_name name) RETURNS text AS
 $body$
 DECLARE ddl text;
  t_ddl record;
  q_attr int;
  counter int;
  t_oid oid;
BEGIN
  ddl := 'CREATE TABLE '||table_name||' (';
  t_oid := oid FROM pg_class where relname = table_name;
  q_attr := count(*) from pg_attribute where attrelid = t_oid and attnum > 0;
  counter :=0;
  FOR t_ddl in
   SELECT pa.attname as atname, pa.attnotnull as notnull, pa.atthasdef as hasdef,
          format_type(pa.atttypid, pa.atttypmod) as typedefn, pad.adsrc as deflt
    FROM pg_attribute pa
    LEFT JOIN pg_type pt ON pa.atttypid = pt.oid
    LEFT JOIN pg_attrdef pad on (pad.adrelid=pa.attrelid and pad.adnum=pa.attnum)
    WHERE pa.attrelid = t_oid AND pa.attnum > 0 ORDER BY attnum
  LOOP
   counter := counter + 1;
   ddl := ddl||'\n'||t_ddl.atname||' ';
   IF t_ddl.deflt like 'nextval(%' THEN ddl := ddl||' serial ';
     ELSE ddl :=  ddl||t_ddl.typedefn;
     IF t_ddl.notnull THEN ddl := ddl||' NOT NULL '; END IF;
     IF t_ddl.hasdef  THEN ddl := ddl||' DEFAULT '||t_ddl.deflt; END IF;
   END IF;
   IF counter < q_attr THEN ddl := ddl||','; END IF;
  END LOOP;
FOR t_ddl IN
   SELECT r.conname, pg_catalog.pg_get_constraintdef(oid, true) as fk
   FROM pg_catalog.pg_constraint r
   WHERE r.conrelid = t_oid AND r.contype IN ('f','p') ORDER BY r.contype DESC, 1
LOOP
  IF FOUND THEN ddl := ddl||',\nCONSTRAINT '||t_ddl.conname||' '||t_ddl.fk; END IF;
END LOOP;
ddl := ddl||'\n)';
SELECT INTO t_ddl relhasoids FROM pg_catalog.pg_class pc WHERE pc.oid = t_oid;
IF t_ddl.relhasoids THEN ddl := ddl||' WITH OIDS;';
ELSE ddl := ddl||' WITHOUT OIDS;'; END IF;
FOR t_ddl IN
 SELECT  pg_catalog.pg_get_indexdef(i.indexrelid, 0, true) AS indx
 FROM pg_catalog.pg_class c, pg_catalog.pg_class c2, pg_catalog.pg_index i
 WHERE c.oid = t_oid AND c.oid = i.indrelid AND i.indexrelid = c2.oid AND NOT i.indisprimary
 ORDER BY c2.relname
LOOP
  IF FOUND THEN ddl := ddl||'\n'||t_ddl.indx||';\n'; END IF;
END LOOP;
FOR t_ddl IN
  SELECT pg_catalog.pg_get_triggerdef(t.oid) AS trgs
  FROM pg_catalog.pg_trigger t
  WHERE t.tgrelid = t_oid AND NOT t.tgisconstraint
LOOP
  IF FOUND THEN ddl := ddl||'\n'||t_ddl.trgs||';\n'; END IF;
END LOOP;
RETURN ddl;
END;
$body$
LANGUAGE 'plpgsql' VOLATILE CALLED ON NULL INPUT SECURITY INVOKER;



CREATE OR REPLACE FUNCTION show_ddl_db () RETURNS text AS
 $body$
 DECLARE
  tbn CURSOR FOR
      SELECT pt.relname
          FROM pg_catalog.pg_class pt
            LEFT JOIN pg_catalog.pg_roles r ON r.oid = pt.relowner
            LEFT JOIN pg_catalog.pg_namespace pn ON pn.oid = pt.relnamespace
          WHERE pt.relkind IN ('r','')
      AND pt.relname NOT LIKE ('pg_ts_%')
      AND pn.nspname NOT IN ('pg_catalog', 'pg_toast')
      AND pg_catalog.pg_table_is_visible(pt.oid);
  db text;
  tb text;
  tb1 name;
  tmp text;
  cnt integer;
BEGIN
  db := '';
SELECT INTO cnt count(*)
FROM pg_catalog.pg_class pt
     LEFT JOIN pg_catalog.pg_roles r ON r.oid = pt.relowner
     LEFT JOIN pg_catalog.pg_namespace pn ON pn.oid = pt.relnamespace
WHERE pt.relkind IN ('r','')
      AND pt.relname NOT LIKE ('pg_ts_%')
      AND pn.nspname NOT IN ('pg_catalog', 'pg_toast')
      AND pg_catalog.pg_table_is_visible(pt.oid);
OPEN tbn;
  LOOP
   EXIT WHEN cnt = 0 ;
   FETCH  tbn INTO tb;
   tb := show_ddl_table(tb);
   db := db||tb||'\n\n\n';
   cnt := cnt - 1;
  END LOOP;
CLOSE tbn;
RETURN db;
END;
 $body$
LANGUAGE 'plpgsql' VOLATILE CALLED ON NULL INPUT SECURITY INVOKER;
