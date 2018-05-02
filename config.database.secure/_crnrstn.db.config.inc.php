<?php
//
// INITIALIZE DATABASE FUNCTIONALITY FOR EACH ENVIRONMENT.
# $this->oMYSQLI_CONN_MGR->addConnection([environment-key], [db-host], [db-user-name], [db-user-pswd], [db-database-name], [optional-db-port]);
#$this->oMYSQLI_CONN_MGR->addConnection('LOCALHOST_PC', '127.0.0.4', 'crnrstn_demo3_un', 'FZ5s8vFAC', 'crnrstn_demo3');
#$this->oMYSQLI_CONN_MGR->addConnection('LOCALHOST_PC', '127.0.0.3', 'crnrstn_demo2_un', 'PwHrwMqCS', 'crnrstn_demo2');
$this->oMYSQLI_CONN_MGR->addConnection('LOCALHOST_PC', 'localhost', 'crnrstn_stage', 'KNUrZUssaZ', 'crnrstn_stage','3306');
$this->oMYSQLI_CONN_MGR->addConnection('LOCALHOST_MAC', 'localhost', 'crnrstn_stage', '9sMZA39heM', 'crnrstn_stage','3306');
$this->oMYSQLI_CONN_MGR->addConnection('CYEXX_SYSTEMS', 'localhost', 'jony5com_crnrstn', 'aXLRswYzTS', 'jony5com_crnrstn','');
#$this->oMYSQLI_CONN_MGR->addConnection('LOCALHOST_PC', 'localhost', 'crnrstn_demo', 'aXNTwYzTS', 'crnrstn_demo', 3306);

?>