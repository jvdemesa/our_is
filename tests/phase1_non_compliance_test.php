<?php
/**
 * Phase 1 harness: exercises the real ID_requests_model Non-Compliance
 * methods without CodeIgniter and WITHOUT touching any database.
 * A fake $this->db stands in for CI's query builder.
 * CLI only - must never be reachable over HTTP.
 */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit('CLI only'); }
error_reporting(E_ALL);
define('BASEPATH', __DIR__);

class CI_Model {
    public $db;
    public function __construct(){}
}

class FakeQuery {
    private $rows; private $num;
    public function __construct($rows, $num = null){ $this->rows=$rows; $this->num=($num===null)?count($rows):$num; }
    public function row(){ return count($this->rows) ? $this->rows[0] : null; }
    public function result(){ return $this->rows; }
    public function num_rows(){ return $this->num; }
}

class FakeDb {
    public $insert_calls=array(); public $batch_calls=array(); public $get_queue=array();
    public $insert_id_value=0; public $affected=0; public $trans_status_value=TRUE;
    public $trans_started=FALSE; public $trans_completed=FALSE;
    public function select($s){ return $this; }
    public function from($t){ return $this; }
    public function where($k,$v=null){ return $this; }
    public function order_by($k,$d='ASC'){ return $this; }
    public function limit($n,$o=0){ return $this; }
    public function get($t=''){ return empty($this->get_queue) ? new FakeQuery(array()) : array_shift($this->get_queue); }
    public function insert($t,$d){ $this->insert_calls[]=array($t,$d); return TRUE; }
    public function insert_batch($t,$d){ $this->batch_calls[]=array($t,$d); return TRUE; }
    public function insert_id(){ return $this->insert_id_value; }
    public function affected_rows(){ return $this->affected; }
    public function trans_start(){ $this->trans_started=TRUE; return TRUE; }
    public function trans_complete(){ $this->trans_completed=TRUE; return TRUE; }
    public function trans_status(){ return $this->trans_status_value; }
}

require 'C:\\laragon\\www\\our_is\\application\\models\\ID_requests_model.php';

$pass=0; $fail=0;
function chk($c,$l){ global $pass,$fail; if($c){ $pass++; echo "PASS: $l\n"; } else { $fail++; echo "FAIL: $l\n"; } }
function model($db){ $r=new ReflectionClass('ID_requests_model'); $m=$r->newInstanceWithoutConstructor(); $m->db=$db; return $m; }
function row($id){ return new FakeQuery(array((object)array('id'=>$id)),1); }
function none(){ return new FakeQuery(array(),0); }

$m=model(new FakeDb());
chk($m->get_non_compliance_file_types()===array('id_picture','signature','form5','first_year_form5','proof_of_payment'),'file type list');
chk($m->is_valid_non_compliance_file_type('form5')===TRUE,'valid file type');
chk($m->is_valid_non_compliance_file_type('bogus')===FALSE,'invalid file type');
chk($m->normalize_non_compliance_file_reasons(array('id_picture'=>'blurry','form5'=>'wrong term'))===array('id_picture'=>'blurry','form5'=>'wrong term'),'normalise associative');
chk($m->normalize_non_compliance_file_reasons(array(
        array('file_type'=>'id_picture','upload_reason'=>'a'),
        array('file_type'=>'id_picture','reason'=>'b'),
        array('file_type'=>'signature','reason'=>'c')))===array('id_picture'=>'b','signature'=>'c'),'normalise list + dedupe');
chk($m->normalize_non_compliance_file_reasons('nope')===array(),'normalise non-array');
chk($m->validate_non_compliance_submission('',array())['errors']!==array(),'reject empty submission');
chk($m->validate_non_compliance_submission('Fix details',array())['errors']===array(),'accept message only');
chk($m->validate_non_compliance_submission('',array('form5'=>''))['errors']!==array(),'reject file without reason');
chk($m->validate_non_compliance_submission('',array('bogus'=>'x'))['errors']!==array(),'reject invalid file id');
$v=$m->validate_non_compliance_submission(null,array('form5'=>'wrong term'));
chk($v['errors']===array() && $v['message']==='','accept files without message');
$db=new FakeDb(); $db->get_queue[]=row(5); $mm=model($db);
chk($mm->is_valid_id_request(5)===TRUE,'valid request id');
chk($mm->is_valid_id_request(0)===FALSE,'reject zero id');
chk($mm->is_valid_id_request('abc')===FALSE,'reject non-numeric id');
$db=new FakeDb(); $db->get_queue[]=none(); $mm=model($db);
chk($mm->is_valid_id_request(999)===FALSE,'reject unknown id');
$db=new FakeDb(); $db->get_queue[]=none(); $mm=model($db);
$r=$mm->save_non_compliance(999,'msg',array());
chk($r['error']===1 && empty($db->insert_calls),'save rejects unknown request');
$db=new FakeDb(); $db->get_queue[]=row(5); $mm=model($db);
$r=$mm->save_non_compliance(5,'   ',array());
chk($r['error']===1 && empty($db->insert_calls),'save rejects empty submission');
$db=new FakeDb(); $db->get_queue[]=row(5); $db->insert_id_value=77; $mm=model($db);
$r=$mm->save_non_compliance(5,'Fix details',array(),42);
chk($r['error']===0 && $r['non_compliance_case_id']===77 && count($db->insert_calls)===1 && empty($db->batch_calls),'save message-only');
chk($db->insert_calls[0][0]==='id_request_non_compliance_messages' && $db->insert_calls[0][1]['non_compliance_message']==='Fix details' && $db->insert_calls[0][1]['created_by']===42,'message row values');
chk($db->trans_started && $db->trans_completed,'transaction used');
$db=new FakeDb(); $db->get_queue[]=row(5); $db->insert_id_value=88; $db->affected=2; $mm=model($db);
$r=$mm->save_non_compliance('5',null,array('id_picture'=>'blurry','form5'=>'wrong term'),null);
chk($r['error']===0 && $r['upload_count']===2,'save files-only');
chk($db->insert_calls[0][1]['non_compliance_message']===null && $db->insert_calls[0][1]['created_by']===null,'null message when files-only');
$rows=$db->batch_calls[0][1];
chk($db->batch_calls[0][0]==='id_request_non_compliance_uploads' && count($rows)===2,'upload rows count');
chk($rows[0]['non_compliance_case_id']===88 && $rows[0]['id_request_id']===5,'uploads grouped by case id');
$db=new FakeDb(); $db->get_queue[]=row(5); $db->insert_id_value=99; $db->trans_status_value=FALSE; $mm=model($db);
$r=$mm->save_non_compliance(5,'msg',array());
chk($r['error']===1,'transaction failure reported');
$db=new FakeDb(); $db->get_queue[]=row(5); $db->get_queue[]=none(); $mm=model($db);
$r=$mm->get_latest_non_compliance(5);
chk($r['error']===0 && $r['found']===FALSE && $r['submission']===null && $r['uploads']===array(),'no history consistent response');
$sub=(object)array('id'=>77,'id_request_id'=>5,'non_compliance_message'=>'Fix','created_at'=>'2026-10-13 10:00:00','created_by'=>1);
$u1=(object)array('id'=>1,'file_type'=>'form5','upload_reason'=>'wrong term');
$db=new FakeDb(); $db->get_queue[]=row(5); $db->get_queue[]=new FakeQuery(array($sub),1); $db->get_queue[]=new FakeQuery(array($u1),1); $mm=model($db);
$r=$mm->get_latest_non_compliance(5);
chk($r['found']===TRUE && $r['submission']->id===77 && count($r['uploads'])===1 && $r['uploads'][0]->file_type==='form5','latest submission + uploads');
$db=new FakeDb(); $db->get_queue[]=none(); $mm=model($db);
$r=$mm->get_latest_non_compliance('a');
chk($r['error']===1 && $r['found']===FALSE,'retrieval rejects invalid id');
chk($mm->get_latest_non_compliance_message('a')===FALSE,'accessor FALSE when invalid');

echo "\nRESULT: $pass passed, $fail failed\n";
exit($fail===0 ? 0 : 1);

