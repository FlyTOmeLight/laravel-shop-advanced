<?php

return [
    'alipay' => [
        'app_id'         => '2016091400507047',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAqA1Fbyp3U0Rn/L8Z46hMswmDlvJfo8sTsSEhWFzdhoJ2bKsCMBc6uQ7ZMZvZJBS7S+2WS3cutS0YCfCXZEO/6iALOc4VqJ0yuQgdpKvkc/ZSeUdRA28kOyWRC3zRv1aSgbMmJsMhNhrePlyn8wUx0M58Hp/OElvTgazaAyis8YFCeIF+X6sY3yWou5S1+EM2fmgF7Snp/yXmyngUBeV8+nXQoUO1WH9S1hx0jWKk/AjpgMZ17o1K37hKWa1U6ihbvjP4/HhZ9R6WvNvPTwoRT/OuVRd1e4rsbLqyyjQJwLrsC/DefnD825Cjg1rrmImk3rCdpnZSC2uUmNVpMa1kewIDAQAB',
        'private_key'    => 'MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCFa2FN/OrqWGtVFMcCaaqwiKkam5PgjEiooQeuP+o2D6ObfwjHV0ff3fYEqj9570QTaCxWWkvTVfxOBiEdtzQJj9YuGy+mc5z2Ht/Pqc+Nvuanm70n/4ZmDBIqosyCA8ubYTwYDgiJPwtSGKWU6OnoMPKBR/CgPLYUHdg6OXgu0VPX4YfLlWabwLTFKFuuo+YI2OrwZzn9zRFWjYDl31LuZ2hrsu2ZYGXq+KdL6APe016iLfm4oZhWaqXu5M4j461P4IJnkt4rnl8Bmqa7QQwPqt7gfKJMtgjUR70t3Cisqko1hnyvciR/m7mbJ7AMYgbcRKuUAsZxOHNLN7BQZIcvAgMBAAECggEAJBtTUg/IY3NaWMQut3BxSSUy3oiMFJDieQBbW8TgVZEV7dcLQSdVxDgFhG2ukAk4myfuF9CIBMGYbmUqQSh7ZLsQc5nX42wWZmUknMaP7QRk16dGIbT9YlCR+gfk4Kq2R26hHMFvffUZ0zXoWcxOwQ++EU6uyC6l9/u7sMYsyD5qJFy7rTkcr0PmPt5QA2ND1PEuRhFmGy0O6eoC4FbERCNl23X8VHqtU6zBMNwoqhfHs3D4od3YziChcQOp/KEqKG+HGgop5o5xOmpnKAwIJu8hGHDycePBEmHYL2LQ6bAoQr1g2xjuJg6W9h6RXGWst1YDND8wPayK3kHqrHkSQQKBgQC/Bx+GsC7PQtMY6nuf2taSczOLJ8WtdnpqXsyTGWxyIv+g8WiOiFF6Bq/n4DK6MhfhzreSsgDqOzoBr1cIO2CRNSBwJho3QjyvUgRiw0pzCaXVmY/lAcGlOVEcI8LIK4iGKLTrZL7G0FV8Oc05eSxOijZKqjhQugN0sooK/52dxQKBgQCyzEVLcT4IgP0AnukGoNT1eO/OiBZYgB4ys/VmM4p7txZX1VeJwWW61xcj6k2Sj/k9J6GlOVduxUIzEAV5OyTnFH2RBUreU9dazVOAGASusHc8qwJKeStPiksd9jHaQv4sKdYE3yvI21Dn+p6ZSgPR/5H6sEBoIqRNE5YTf5q0YwKBgDXJdqoA750yLqgkVdzDzIj94PR8pV9bLcrHmIiOrwdQLXQOpScE4RQfz+XVLdNZiXnuL6ghcLqDJKkWysWpml4ofFK3l8gZYWboDA4W+N59R/FlxKtxnCm+gsUOmKiuAUntKkvhWQo33OoWiF93a+9NYU4SsbqhNZnlL5M7OBEVAoGAU2w/wK17lhgo88sxSqhr9ISSzzhrxKsya9HPY/oUWRjZ0e12xI15261T3KwLuaB3V/NP1nlktAhRlOWayC+yTicfVzSy8/0GdBVtKk0Kkj8/wwy/KuRSMvCBj984729mjFCQzQEbusGphDNJIJiKI8cs41ytEGlpY5UJ5tsY4aUCgYBykSZtQLdmJ41EJZuX4mJ/AVrTTFudXOrg5qvodN2pmD0kOwfj9xeZsGHduRS1DG2upgI5tC4ivQNSMbsRaFJyxU+tFg07PNpYZ1aWuVDJP3E9j1ydwiinXTYxUhIJjZar1n+8aVlPe/ie2mdLxgeBWghUuFmD3n+3npqJNFPIeg==',
        'log'            => [
            'file' => storage_path('logs/alipay.log'),
        ],
    ],

    'wechat' => [
        'app_id'      => '',
        'mch_id'      => '',
        'key'         => '',
        'cert_client' => '',
        'cert_key'    => '',
        'log'         => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],
];
