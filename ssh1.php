<?php
$pass='Qutaibah5544@'; $mode=$argv[1]??'run';
$pf=sys_get_temp_dir().'/ssh_askpass1.bat';
file_put_contents($pf,"@echo off\r\necho ".str_replace(['^','&','|','<','>','(',')'],['^^','^&','^|','^<','^>','^(','^)'],$pass)."\r\n");
$env=getenv(); $env['SSH_ASKPASS']=$pf; $env['SSH_ASKPASS_REQUIRE']='force'; $env['DISPLAY']='dummy:0';
$base='-o StrictHostKeyChecking=no -o BatchMode=no -o PasswordAuthentication=yes -o PreferredAuthentications=password';
$cmd = $mode==='scp' ? 'scp -P 65002 '.$base.' u714104226@82.112.252.128:'.escapeshellarg($argv[2]).' '.escapeshellarg($argv[3]) : 'ssh -p 65002 '.$base.' u714104226@82.112.252.128 '.escapeshellarg($argv[2]??'echo OK');
$d=[0=>['pipe','r'],1=>['pipe','w'],2=>['pipe','w']]; $p=proc_open($cmd,$d,$pi,null,$env);
fclose($pi[0]); $o=stream_get_contents($pi[1]); $e=stream_get_contents($pi[2]); fclose($pi[1]); fclose($pi[2]); $rc=proc_close($p);
echo $o.$e;
