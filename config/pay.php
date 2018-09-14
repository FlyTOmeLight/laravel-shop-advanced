<?php

return [
    'alipay' => [
        'app_id'         => '2016091400507047',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAuwjSt3/IyREooETi+6z1Apku0K8vXtYZtJYYpidoVzBGxArm1lBshWzd1T4LjXlQ1mdZoj/gPUQRCSsRo4jXllfeuW5PgEWh0Ybfo/a5t1RbTeWAekJr7FJusHmRDAev5f5oWJNvpzQzSJvS2NelHqQ9bqLMPUQb+oJZeO80Fdmy56+dFj5199POp/aQwWydr1VB6ynHj0VLQ59K97CUqXf0biCMIvpOPdMYedz4noXD75p/m+gbKa2E0LsYsyQrXph2nPHHMnypaOwRmUgzOSv5LsxXqgrmrMvLaJs4CERdKMiBzpPlEj5KA+/P2R6x6MPNV9EsnXKSMFdNmD2RSwIDAQAB',
        'private_key'    => 'MIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQC7CNK3f8jJESigROL7rPUCmS7Qry9e1hm0lhimJ2hXMEbECubWUGyFbN3VPguNeVDWZ1miP+A9RBEJKxGjiNeWV965bk+ARaHRht+j9rm3VFtN5YB6QmvsUm6weZEMB6/l/mhYk2+nNDNIm9LY16UepD1uosw9RBv6gll47zQV2bLnr50WPnX3086n9pDBbJ2vVUHrKcePRUtDn0r3sJSpd/RuIIwi+k490xh53PiehcPvmn+b6BsprYTQuxizJCtemHac8ccyfKlo7BGZSDM5K/kuzFeqCuasy8tomzgIRF0oyIHOk+USPkoD78/ZHrHow81X0SydcpIwV02YPZFLAgMBAAECggEBAKK+wKwPMuVSWulqR5/7FY7XP9cyOKPq5J8wY+5gJ/iF922qlsYxYNyQqE5fGKpXv7FwaJw3vKVSzwgNQ+Hqtr0JZLIxfFf/PxkUpREFJQCTFIephavclBAkglBo13+CSNp2DtHUKLlzQJSTklpA+lq5SwM0AUfmNfo65iPG8Kl5MBiDI45/ZhyTgI1joDwAf9BrLmXXHy8MYzp4wjVc9orDzDNdmsRkK9YGujfZ7oHSsljQEWxzsWOBDKerG5e+Zc6N+mwjxOkJEV3OB1Iq9tkqBjtFnlzRW/+K1rMq3yqpjlSFAaOLuHS7Sv1vMLGNCXyyOTPOfwTxQyG4bjQLyMECgYEA8MjjWw6i2H25laHnns9iKWFJEenMpI4rvVXsDmFp+QLy/mst5Hdn7D0d1erzzCnyVTfJk+GDotudyV8/+/l522mu+mRacc9z0rQ0/xuqKT0NX/oegYTN+DNQvgClH/+kQn3qApl0i/KREvx061Ccr2wOBkM2yu9xjPxNwvtr9/ECgYEAxtpuXKb148usLcv1Z0Imxdw03WQOXW9IwJ3R2Y4eQnqGHWdBRyKH53Ay5KINUaUUoV3+pYPVvd0oFvvB+QYCIba/MVmYQOvrMLpfzCmNOhlrxSq/6V29dkuNd0UZqiZN+875XlE6Q+flK1qlUujtfCnAVi+6YHF2LboocgAw+PsCgYBsJg7YS06hZncA2mOqVQOGCohDX3vnPWQv+nO4UdDDY6u40nMedvNajGjmOE2gmgaaeCA2J8h0UaghVoLqrjcpceKB3KkzDTa8oOxc2RQoyZ9ESJeDHR9WJ9ZQQQHpyW0B4IRG/p32bdDzcikhuGdn82SpM29c3wdlh711om/wQQKBgQCUAYKBLbf42+CEmblHKOKJBFTIr69NdgX1b8IAppn5Kw7llbPtiVMUPNt0dvVo3nEThk2BzjVpwtETAkGm4wT1KPr3dVMwhygftaiV4ht+sesX7rC2tpHNGYDq1CC9FxOWJ8BNpjGy10goU6iXpE0WzFCfFZkoLWsjr9pot6G7vwKBgQCnNSFi3JiMBt4DLf5WgQyM0cV2DW9TISbN244FDlDk7Us3sjphSpEQ6jTXlRtQr+oYYzw940WZfGGgr4+IfUKLJSisPBCzdH4PDOfl6CFG8oMZHKyR1um0qOUwwDbv8a9XMN7fdifsileHUVFkUjvwYh3m+65dvSKjTN+r8wiWdQ==',
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
