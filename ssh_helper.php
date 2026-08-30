<?php
$pass = "Qutaibah5544@";
$passFile = sys_get_temp_dir() . '/ssh_pass.bat';
file_put_contents($passFile, "@echo off\r\necho " . $pass . "\r\n");

$env = [
    'SSH_ASKPASS' => $passFile,
    'DISPLAY' => 'dummy:0',
    'PATH' => getenv('PATH'),
];

$desc = [
    0 => ['pipe', 'r'],
    1 => ['pipe', 'w'],
    2 => ['pipe', 'w'],
];

$cmd = 'ssh -p 65002 -o StrictHostKeyChecking=no -o PasswordAuthentication=yes -o PreferredAuthentications=password u368322281@141.136.39.89 echo HOSTINGER_SSH_OK';

$proc = proc_open($cmd, $desc, $pipes, null, $env);
if (!$proc) {
    echo "FAILED_TO_START\n";
    exit(1);
}

fclose($pipes[0]);
$stdout = stream_get_contents($pipes[1]);
$stderr = stream_get_contents($pipes[2]);
fclose($pipes[1]);
fclose($pipes[2]);
$rc = proc_close($proc);

file_put_contents('ssh_result.txt', "RC=$rc\nSTDOUT=" . trim($stdout) . "\nSTDERR=" . trim($stderr) . "\n");
