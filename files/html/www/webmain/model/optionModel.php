<?php
/**
*	来自：信呼开发团队
*	作者：磐石(rainrock)
*	网址：http://www.rockoa.com/
*	系统文件
*/
class optionClassModel extends Model { private $getypsarr = array(); public function getval($num, $dev='', $lx=0) { $val= ''; $rs = $this->getone("`num`='$num'", '`name`,`value`,`id`,`optdt`'); if($rs){ if($lx==0)$val=$rs['value']; if($lx==1)$val=$rs['name']; if($lx==2)$val=$rs['id']; if($lx==3)$val=$rs['optdt']; } if(isempt($val))$val=$dev; return $val; } public function getpids($num) { if(!is_numeric($num)){ $id = (int)$this->getmou('id', "`num`='$num'"); if($id == 0)$id = -829; }else{ $id = $num; } return $id; } public function getdata($num, $whe='') { $pid = $this->getpids($num); return $this->getall("`pid`='$pid' and `valid`=1 $whe order by `sort`,`id`",'`id`,`name`,`value`,`num`,`receid`,`recename`,`pid`'); } public function getmnum($num) { return $this->getdata($num); } public function getselectdata($num, $tbo=false) { $this->getselectdatad = array(); $this->getselectdatas($num,0, $tbo); return $this->getselectdatad; } public function getselectdatas($num,$lev=0, $tbo=false) { $pid = $this->getpids($num); $fied= ''; if($tbo)$fied=',(select count(1) from `[Q]option` where `pid`=a.`id` and `valid`=1)as stotal'; $arr = $this->db->getall("select a.`id`,a.`name`,a.`num`,a.`value`".$fied." from `[Q]option` a where a.`pid`='$pid' and a.`valid`=1 order by a.`sort`,a.id"); foreach($arr as $k=>$rs){ $rs['nameo'] = $rs['name']; $strs = ''; for($i=0;$i<$lev;$i++)$strs.='&nbsp;&nbsp;&nbsp;&nbsp;'; if($lev>0)$rs['name'] = ''.$strs.'├'.$rs['name'].''; $rs['padding'] = $lev*24; $this->getselectdatad[] = $rs; if($tbo && $rs['stotal']>0)$this->getselectdatas($rs['id'], $lev+1, $tbo); } } public function setval($num, $val='', $name=null, $isub=true) { $numa = explode('@', $num); $num = $numa[0]; $where = "`num`='$num'"; $id = (int)$this->getmou('id', $where); if($id==0)$where=''; $arr = array( 'num' => $num, 'value' => $val, 'optid' => $this->adminid, 'comid' => m('admin')->getcompanyid(), 'optdt' => $this->rock->now ); if(isset($numa[1]))$arr['pid'] = $numa[1]; if($name!=null)$arr['name'] = $name; if($id==0 || $isub)$this->record($arr, $where); if($id==0)$id = $this->db->insert_id(); return $id; } public function gettreedata($pid) { $rows = $this->getfoldrowsss($pid); return $rows; } public function getfoldrowsss($pid) { $rows = $this->db->getall("select `id`,`pid`,`name`,`optdt`,`sort`,`receid`,`recename` from [Q]option where `pid`='$pid' and `valid`=1 order by `sort`,`id`"); foreach($rows as $k=>$rs){ $rows[$k]['expanded'] = true; $rows[$k]['children'] = $this->getfoldrowsss($rs['id']); } return $rows; } public function getnumtoid($num, $name='', $isub=true) { $idd = $this->setval($num,'', $name, $isub); return $idd; } public function getpidarr($pid, $lx=0) { $rows = $this->getall("`pid`='$pid'"); $barr = array(); foreach($rows as $k=>$rs){ $barr[$rs['num']] = $rs['value']; } return $barr; } public function getalldownid($id) { $str = $id; $rows = $this->getall('`pid`='.$id.' and `valid`=1','`id`'); foreach($rows as $k=>$rs){ $str1= $this->getalldownid($rs['id']); $str.=','.$str1.''; } return $str; } public function getreceiddownall($uid, $optid=0, $type=0) { $rstr = m('admin')->getjoinstr('`receid`', $uid, 1,1); $whe = ''; if($optid>0)$whe=' and `optid`='.$optid.''; $rows = $this->getall('`valid`=1 and `type`='.$type.' and ('.$rstr.') '.$whe.'','`id`'); $strs = ''; foreach($rows as $k=>$rs){ $str1 = $this->getalldownid($rs['id']); $strs.=','.$str1.''; } if($strs!='')$strs = substr($strs, 1); return $strs; } public function gettypeid($djnum,$s) { if(isset($this->getypsarr[$s]))return $this->getypsarr[$s]; $sid = 0; $s = str_replace(',','/', $s); $djid= $this->getval($djnum,'0',2); if(isempt($djid)){ $djid = $this->insert(array('name' => '分类','num' => $djnum,'pid'=> 0,'valid'=> 1)); } $dsja= $djid; $sarr= explode('/', $s); foreach($sarr as $safs){ $pid = $djid; $djid = (int)$this->getmou('id', "`pid`='$pid' and `name`='$safs'"); if($djid==0){ $djid = $this->insert(array( 'name' => $safs, 'pid' => $pid, 'valid' => 1, )); } } if($djid != $dsja)$sid = $djid; $this->getypsarr[$s] = $sid; return $sid; } public function delpid($pid) { $this->delete("`pid` in($pid)"); } public function getcnumdata($num) { if(ISMORECOM && $cnum=m('admin')->getcompanynum())$num.='_'.$cnum.''; $rows = $this->getselectdata($num, true); $arr = array(); foreach($rows as $k=>$rs){ $arr[] = array( 'value' => $rs['id'], 'name' => $rs['name'], 'padding' => $rs['padding'], ); } return $arr; } public function addoption($name, $num, $pid=0, $down=array(), $arr=array()) { $id = (int)$this->getmou('id',"`num`='$num'"); if(!$id)$id=0; if($id>0)return $id; if(!is_numeric($pid))$pid = (int)$this->getmou('id',"`num`='$pid'"); if($pid==0)$pid = 1; $uarr = array( 'num' => $num, 'pid' => $pid, 'name' => $name, ); foreach($arr as $k=>$v)$uarr[$k]=$v; $id = $this->insert($uarr); if($down && is_string($down)){ $downa = explode(',', $down);$down = array(); foreach($downa as $k2)$down[]=array('name'=>$k2); } if($down)foreach($down as $k=>$rs){ $iarr = $rs; $iarr['pid'] = $id; $idown= arrvalue($rs, 'down'); if($idown){ unset($iarr['down']); $sid = $this->insert($iarr); foreach($idown as $k1=>$rs1){ $isarr = $rs1; $isarr['pid'] = $sid; $this->insert($isarr); } }else{ $this->insert($iarr); } } return $id; } public function authercheck() { $rows = $this->getall('pid=-101','`num`,`value`'); $authkey = $yuming = $enddt = ''; foreach($rows as $k1=>$rs1){ if($rs1['num']=='auther_authkey')$authkey = $rs1['value']; if($rs1['num']=='auther_yuming')$yuming = $rs1['value']; if($rs1['num']=='auther_enddt')$enddt = $rs1['value']; } if(isempt($yuming) || isempt($enddt) || isempt($authkey))return $this->rock->jm->base64decode('57O757uf5pyq562!5o6I5LiN6IO95L2.55So77yM562!5o6I5piv5YWN6LS555qE77yMPGEgaHJlZj0iaHR0cDovL3d3dy5yb2Nrb2EuY29tL3ZpZXdfYXV0aGVyLmh0bWwiPueci!W4ruWKqeiuvue9rjwvYT4:'); if($this->rock->jm->uncrypt($enddt)<$this->rock->date)return $this->rock->jm->base64decode('57O757uf562!5o6I5bey5Yiw5pyf'); $ym = $this->rock->jm->uncrypt($yuming); $mym = HOST; $ho1 = ','.$mym.','; $yma = explode(',', $ym); $bool = false; foreach($yma as $ym1){ if($ym1==$mym)$bool=true; if(substr($ym1,0,2)=='*.' && contain($mym, substr($ym1,1)))$bool=true; } if(substr($mym,0,7)==$this->rock->jm->base64decode('MTkyLjE2OA::'))$bool=true; if(!$bool)return str_replace('1', $mym, $this->rock->jm->base64decode('MeWfn!WQjeacquetvuaOiOS4jeiDveS9v!eUqA::')); return array( 'star' => 'rock', 'authkey' => $authkey, 'yuming' => $ym ); } public function strsste() { } }