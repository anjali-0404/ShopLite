param(
  [Parameter(Mandatory=$true)] [string]$Host,
  [Parameter(Mandatory=$true)] [string]$User,
  [Parameter(Mandatory=$true)] [string]$TargetPath,
  [Parameter(Mandatory=$true)] [string]$AppUrl,
  [Parameter(Mandatory=$true)] [string]$DbHost,
  [Parameter(Mandatory=$true)] [string]$DbName,
  [Parameter(Mandatory=$true)] [string]$DbUser,
  [Parameter(Mandatory=$true)] [string]$DbPass,
  [int]$SshPort = 22,
  [switch]$IncludeSchema
)

$repoRoot = Split-Path -Parent (Split-Path -Parent $MyInvocation.MyCommand.Path)
$tmp = Join-Path $env:TEMP ("shoplite-" + [guid]::NewGuid().ToString())
New-Item -ItemType Directory -Path $tmp | Out-Null
Copy-Item -Path (Join-Path $repoRoot '*') -Destination $tmp -Recurse -Force

$configPhp = @"
<?php
return [
  'DB_HOST' => '$DbHost',
  'DB_NAME' => '$DbName',
  'DB_USER' => '$DbUser',
  'DB_PASS' => '$DbPass',
  'APP_DEBUG' => false,
  'APP_URL' => '$AppUrl'
];
"@
Set-Content -Path (Join-Path $tmp 'config\config.php') -Value $configPhp -Encoding UTF8

$sshCmd = "ssh -p $SshPort $User@$Host"
$scpCmd = "scp -P $SshPort"

& $sshCmd "mkdir -p $TargetPath" | Out-Null
& $scpCmd -r (Join-Path $tmp '*') "$User@$Host:$TargetPath/"

if ($IncludeSchema) {
  $importCmd = "mysql -u$DbUser -p'$DbPass' -h $DbHost $DbName < $TargetPath/db/schema.sql"
  & $sshCmd $importCmd
}

Remove-Item -Recurse -Force $tmp
Write-Output "Deploy complete: $User@$Host -> $TargetPath"