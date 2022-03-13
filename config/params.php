<?php


$host = '';
if (!empty($_SERVER['HTTP_HOST'])) {
    $host = $_SERVER['HTTP_HOST'];
}

return [
    'adminEmail'    => 'admin@example.com',
    'senderEmail'   => 'noreply@example.com',
    'senderName'    => 'Example.com mailer',
    'supportEmail'  => "admin@eyadat.com",
    'supportPhone'  => "1234567890",
    'appName'       => "Eyadat",
    'siteEmail'     => "admin@eyadat.com",
    'websiteLink'   => 'http://54.74.74.105/',
    'websiteName'   => 'http://54.74.74.105/',
    //'googleMapKey' => 'AIzaSyB_Z1RYSuUinx-_BDSoh6H4qiFvhz6Il-c',
    'googleMapKey' => 'AIzaSyDbFTtBfXl7DRtre2cBo4zqVUcUiu9JZbM',
    'allowed_cancel_minutes' => 15,
    'timezone' => 'Asia/Kuwait',
    'is_enable_cdn' => false,
    'bufferQty' => 0,
    'websiteName' => 'Eyadat.com',
    'default_currency_code' => "KW",
    'default_currency' => 82,
    'express_delivery_start_gmt' => '05:00:00',
    'express_delivery_end_gmt' => '14:00:00',
    'myfatoorahToken' => ($host == 'admin.shop-twl.com') ? 'nAq5O3WvzJ-rXcITDsQ5d6oxY289PSrcqfqReJjpXux3XwPyoEdlO4t85IaVwlokxnorXsz3EFrO_ZmqS8lP-oAE20-tOKO_RjCned2ydrGC1kZn-antJYvbyqXXRPO5Kisrzi9XEO1GiLhFRTLdQZ2umtphdvV6k5CYuZJoowvKiEAtLbRsYh1fBd2GrOD659SRnbqRXRGWujf7owMWNKlbMpLFXhpZHlg2pSp5dVMTRK2gy07VB68Cu4Svbws9BhCVykGVHwW0Fcyn5TkCuPtBltaspQTZ2jOIeviv0r_cm469xWq5OGsoyLChGs0veNb3xFPViBSew4wL6fAhlj28BqfESjcAuPDTSdF93ymrZqbRBK8YgDbi-9ycmGjwSanPTkuwaVp8x0iobHbWEfEKrah2l-LfmP2A5I6ChjOKGRHmkbWJtvXmxvPWSPv44yteXCUue0Fenw800Q9VF0BQY5aZswani50P791lKAK9ifZr_rXE6yirNzAyIsWGjnohPOcT9nnGnbz1dGEKiXEPt1AqJ0oYkSUUEVWxTmRVm1joU9qcZ85pqA5dxA_lDNFJzlJREfpW37xfMevEzPwqEpkAfEAlPsPgwh7LRjwVeTbq6m4Yv38p01nju-7p3wvf6rHDz3alMUbwpm00eiS2B08wPrv4HieW240hwu6kEyZg' : 'rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL',
    'myfatoorahExecuteToken' => ($host == 'admin.shop-twl.com') ? 'nAq5O3WvzJ-rXcITDsQ5d6oxY289PSrcqfqReJjpXux3XwPyoEdlO4t85IaVwlokxnorXsz3EFrO_ZmqS8lP-oAE20-tOKO_RjCned2ydrGC1kZn-antJYvbyqXXRPO5Kisrzi9XEO1GiLhFRTLdQZ2umtphdvV6k5CYuZJoowvKiEAtLbRsYh1fBd2GrOD659SRnbqRXRGWujf7owMWNKlbMpLFXhpZHlg2pSp5dVMTRK2gy07VB68Cu4Svbws9BhCVykGVHwW0Fcyn5TkCuPtBltaspQTZ2jOIeviv0r_cm469xWq5OGsoyLChGs0veNb3xFPViBSew4wL6fAhlj28BqfESjcAuPDTSdF93ymrZqbRBK8YgDbi-9ycmGjwSanPTkuwaVp8x0iobHbWEfEKrah2l-LfmP2A5I6ChjOKGRHmkbWJtvXmxvPWSPv44yteXCUue0Fenw800Q9VF0BQY5aZswani50P791lKAK9ifZr_rXE6yirNzAyIsWGjnohPOcT9nnGnbz1dGEKiXEPt1AqJ0oYkSUUEVWxTmRVm1joU9qcZ85pqA5dxA_lDNFJzlJREfpW37xfMevEzPwqEpkAfEAlPsPgwh7LRjwVeTbq6m4Yv38p01nju-7p3wvf6rHDz3alMUbwpm00eiS2B08wPrv4HieW240hwu6kEyZg' : 'rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL',
    'myfatoorahUrl' => "apitest", //($_SERVER['HTTP_HOST'] == 'admin.3eyadat.com') ? "api":"apitest",

    'secret_api_key' => 'vz178pldcutk2ez4dzo3askdfbak32re',
    'secret_api_iv' => '6498hfvuyr82623a',


];
