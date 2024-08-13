param (
    [Parameter(Mandatory)] $arch
)

$ErrorActionPreference = "Stop"

$suffix = if ($arch -eq "x64") {"windows"} else {"nt"}
$url = "https://download.oracle.com/otn_software/nt/instantclient/instantclient-sdk-$suffix.zip"
Invoke-WebRequest $url -OutFile "instantclient-sdk.zip"
7z x "instantclient-sdk.zip"
Move-Item "instantclient_*" "instantclient"
