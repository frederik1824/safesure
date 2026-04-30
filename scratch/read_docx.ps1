Add-Type -AssemblyName System.IO.Compression.FileSystem
$zip = [System.IO.Compression.ZipFile]::OpenRead('c:\Users\frede.FREDERIKLOPEZ18\Desktop\Deploy\sys_safe_carnet\Pendientes Frederik\REGLAS.docx')
$entry = $zip.GetEntry('word/document.xml')
$reader = New-Object System.IO.StreamReader($entry.Open())
$xml = $reader.ReadToEnd()
$reader.Close()
$zip.Dispose()
$text = $xml -replace '<[^>]+>', ' '
$text -replace '\s+', ' ' | Out-File "scratch/docx_output.txt"
