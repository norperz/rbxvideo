<html>
<head>
<title>ROBLOX Asset Info API</title>
</head>
<body>

    <?php
        $id = $_GET['id'];
        $shouldSearch = true;
        $peeps = array(54420742, 74183525, 87212563, 2975816);

        for ($i = 0; $i <= 20; $i++) {
            $id -= 1;
            if ($shouldSearch) {
                $xml = json_decode(file_get_contents("https://api.roblox.com/Marketplace/ProductInfo?assetId=".$id));
                $creator = $xml->Creator->Id;
                if (in_array($creator, $peeps)) {
                    $shouldSearch = false;
                    echo $id;
                }
            }
        }
    ?>

</body>
</html>
