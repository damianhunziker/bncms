<?php
function findOffice ($plz) {
	global $outSitz;
$arrayWohlen=array(
4303,
4305,
4310,
4312,
4313,
4314,
4315,
4316,
4317,
4322,
4323,
4324,
4325,
4332,
4333,
4334,
4663,
4665,
4800,
4802,
4803,
4805,
4812,
4813,
4814,
4852,
4853,
4853,
4856,
5018,
5022,
5023,
5024,
5025,
5026,
5027,
5028,
5032,
5033,
5034,
5035,
5036,
5037,
5040,
5042,
5043,
5044,
5046,
5053,
5053,
5054,
5054,
5056,
5057,
5058,
5062,
5063,
5064,
5070,
5072,
5073,
5074,
5075,
5076,
5077,
5079,
5080,
5082,
5083,
5084,
5085,
5102,
5103,
5103,
5105,
5106,
5107,
5108,
5112,
5113,
5116,
5210,
5212,
5213,
5222,
5223,
5224,
5225,
5233,
5234,
5235,
5236,
5237,
5242,
5242,
5242,
5243,
5244,
5245,
5246,
5272,
5273,
5274,
5275,
5276,
5277,
5300,
5301,
5303,
5304,
5305,
5306,
5312,
5313,
5314,
5315,
5316,
5316,
5317,
5318,
5322,
5323,
5324,
5325,
5326,
5330,
5332,
5333,
5334,
5406,
5408,
5412,
5413,
5415,
5416,
5417,
5422,
5423,
5424,
5425,
5426,
5430,
5432,
5436,
5442,
5443,
5444,
5445,
5452,
5453,
5454,
5462,
5463,
5464,
5465,
5466,
5467,
5502,
5503,
5504,
5505,
5506,
5507,
5512,
5522,
5524,
5524,
5525,
5600,
5600,
5603,
5604,
5605,
5606,
5607,
5608,
5610,
5611,
5612,
5613,
5614,
5615,
5616,
5617,
5618,
5619,
5619,
5620,
5621,
5622,
5623,
5624,
5624,
5625,
5626,
5627,
5628,
5630,
5632,
5634,
5634,
5636,
5637,
5637,
5642,
5643,
5643,
5644,
5645,
5646,
5647,
5702,
5703,
5704,
5705,
5706,
5707,
5708,
5712,
5722,
5723,
5724,
5725,
5726,
5727,
5728,
5732,
5733,
5734,
5736,
5737,
5742,
5745,
6042,
8905,
8905,
8905,
8916,
8917,
8918,
8919,
8956,
8957,
8962,
8963,
8964,
8965,
8966,
8967
);



	$arrayHerrli=array(
	8102,
8103,
8104,
8105,
8105,
8106,
8107,
8108,
8112,
8113,
8114,
8115,
8117,
8118,
8121,
8122,
8123,
8124,
8125,
8126,
8127,
8132,
8132,
8133,
8134,
8135,
8136,
8142,
8143,
8152,
8152,
8153,
8154,
8155,
8156,
8157,
8158,
8162,
8164,
8165,
8165,
8165,
8166,
8172,
8173,
8174,
8175,
8180,
8181,
8182,
8184,
8185,
8187,
8192,
8192,
8193,
8194,
8195,
8196,
8197,
8246,
8247,
8248,
8302,
8303,
8304,
8305,
8306,
8307,
8307,
8308,
8309,
8310,
8311,
8312,
8314,
8315,
8317,
8320,
8321,
8322,
8330,
8331,
8332,
8335,
8335,
8340,
8342,
8344,
8345,
8352,
8353,
8400,
8412,
8413,
8414,
8415,
8416,
8418,
8421,
8422,
8424,
8425,
8426,
8427,
8427,
8428,
8442,
8444,
8447,
8450,
8451,
8452,
8453,
8457,
8458,
8460,
8461,
8462,
8463,
8465,
8466,
8467,
8468,
8468,
8471,
8471,
8472,
8474,
8475,
8476,
8477,
8478,
8479,
8482,
8484,
8484,
8486,
8487,
8488,
8492,
8493,
8494,
8496,
8497,
8498,
8499,
8523,
8542,
8544,
8545,
8548,
8600,
8602,
8603,
8604,
8605,
8606,
8606,
8607,
8608,
8610,
8614,
8614,
8615,
8616,
8617,
8618,
8620,
8624,
8625,
8626,
8627,
8630,
8632,
8633,
8634,
8635,
8636,
8637,
8700,
8702,
8703,
8704,
8706,
8706,
8707,
8708,
8712,
8713,
8714,
8800,
8802,
8803,
8804,
8805,
8810,
8815,
8816,
8820,
8824,
8825,
8833,
8902,
8903,
8904,
8906,
8907,
8908,
8909,
8910,
8911,
8912,
8913,
8914,
8914,
8915,
8925,
8926,
8932,
8933,
8934,
8942,
8951,
8952,
8953,
8954,
8955,
6402,
6403,
6405,
6410,
6410,
6410,
6410,
6414,
6415,
6416,
6417,
6418,
6422,
6423,
6424,
6430,
6436,
6432,
6433,
6434,
6436,
6436,
6438,
6440,
6442,
6443,
6452,
8806,
8807,
8808,
8832,
8834,
8835,
8836,
8840,
8840,
8849,
8846,
8847,
8841,
8844,
8845,
8842,
8843,
8852,
8853,
8854,
8854,
8855,
8856,
8857,
8858,
8862,
8863,
8864,
8832,
6410,
6431,
8640,
8252,
8254,
8259,
8253,
8255,
8259,
8259,
8264,
8265,
8266,
8267,
8268,
8272,
8273,
8274,
8280,
8280,
8280,
8280,
8355,
8356,
8357,
8360,
8362,
8363,
8370,
8372,
8374,
8374,
8376,
8376,
8500,
8500,
8500,
8505,
8506,
8507,
8508,
8508,
8512,
8512,
8514,
8514,
8522,
8524,
8525,
8526,
8532,
8524,
8532,
8535,
8536,
8537,
8546,
8547,
8552,
8553,
8554,
8554,
8555,
8556,
8564,
8558,
8269,
8560,
8561,
8565,
8566,
8566,
8564,
8564,
8570,
8570,
8572,
8573,
8574,
8574,
8575,
8576,
8577,
9217,
8580,
8580,
8580,
8580,
8581,
8588,
8589,
8583,
8583,
8583,
8584,
8584,
8585,
8585,
8585,
8585,
8585,
8586,
8586,
8586,
8586,
8587,
8590,
8599,
8592,
8593,
8594,
8595,
8596,
8597,
8598,
9213,
9214,
9216,
9215,
9215,
9220,
9223,
9223,
9216,
9225,
9225,
9306,
9315,
9314,
9315,
9320,
9320,
9320,
9322,
9325,
9326,
9502,
9503,
9504,
9506,
9507,
9508,
9514,
9515,
8577,
9517,
9565,
9532,
9535,
9542,
9543,
9545,
9546,
9547,
9548,
9553,
9554,
9555,
9556,
9562,
9565,
9565,
9565,
8370,
9573,
8288,
8580,
8586,
8586,
8586,
8253,
8546,
8553,
8553,
8553,
8573,
8573,
8566,
8566,
8274,
8585,
8585,
8564,
9562,
9556,
9508,
9508,
8512,
8360,
8537,
8505,
8507,
8259,
8514,
8514,
8572,
8572,
8585,
8572,
8585,
8575,
8556,
8556,
8525,
8553,
8500,
8268,
8507,
8522,
9225,
9503,
8574,
8510,
9214,
8285,
8580,
9320,
9220,
8280,
8590,
8570,
8520,
8564,
8564,
8586,
9565,
8514,
6300,
6330,
6313,
6313,
6319,
6315,
6312,
6313,
6314,
6314,
6315,
6315,
6300,
6317,
6318,
6330,
6331,
6332,
6340,
6343,
6345,
6340,
6349,
6343,
6343,
6343,
7310,
7317,
7317,
7314,
7315,
7312,
7313,
7320,
7325,
7326,
7323,
7324,
8638,
8640,
8640,
8640,
8650,
8645,
8646,
8715,
8716,
8717,
8718,
8722,
8723,
8725,
8726,
8727,
8730,
8732,
8733,
8734,
8735,
8735,
8737,
8738,
8739,
8740,
8872,
8873,
8877,
8878,
8880,
8881,
8881,
8881,
8882,
8883,
8884,
8885,
8887,
8886,
8889,
8888,
8890,
8892,
8893,
8894,
8895,
8896,
8897,
8898,
9000,
9030,
9032,
9033,
9034,
9036,
9113,
9114,
9115,
9116,
9122,
9123,
9125,
9126,
9127,
9633,
9200,
9203,
9204,
9205,
9212,
9230,
9230,
9230,
9231,
9604,
9604,
9240,
9240,
9242,
9248,
9243,
9244,
9245,
9246,
9247,
9249,
9302,
9303,
9304,
9305,
9308,
9312,
9313,
9323,
9327,
9400,
9404,
9400,
9402,
9403,
9422,
9423,
9424,
9425,
9430,
9434,
9435,
9436,
9437,
9450,
9442,
9443,
9444,
9445,
9450,
9451,
9452,
9453,
9462,
9463,
9464,
9464,
9465,
9466,
9467,
9468,
9469,
9470,
9470,
9472,
9472,
9473,
9475,
9476,
9477,
9478,
9479,
9500,
9500,
9500,
9500,
9512,
9523,
9524,
9525,
9526,
9527,
9533,
9534,
9536,
9552,
9601,
9602,
9602,
9606,
9607,
9608,
9612,
9613,
9614,
9615,
9620,
9621,
9622,
9630,
9631,
9633,
9642,
9643,
9650,
9651,
9655,
9652,
9656,
9657,
9658,
9470,
9030,
9122,
9478,
8200,
8219,
8228,
8231,
8234,
8235,
8236,
8236,
8242,
8242,
8243,
8239,
8212,
8213,
8214,
8215,
8216,
8217,
8218,
8222,
8223,
8224,
8225,
8226,
8232,
8233,
8240,
8241,
8260,
8261,
8262,
8263,
8454,
8455

	
	);
	
	$arrayHerzog=array(
4050,
4051,
4052,
4053,
4054,
4055,
4056,
4057,
4058,
4059,
4001,
4102,
4103,
4104,
4105,
4106,
4107,
4117,
4123,
4124,
4127,
4132,
4133,
4142,
4144,
4147,
4148,
4153,
4202,
4203,
4222,
4223,
4224,
4225,
4242,
4243,
4244,
4246,
4253,
4254,
4302,
4304,
4402,
4410,
4411,
4414,
4415,
4416,
4417,
4207,
4418,
4419,
4422,
4423,
4424,
4425,
4426,
4431,
4432,
4433,
4434,
4435,
4436,
4436,
4437,
4438,
4441,
4442,
4443,
4444,
4445,
4446,
4447,
4448,
4450,
4451,
4452,
4453,
4455,
4456,
4457,
4458,
4460,
4461,
4462,
4463,
4464,
4465,
4466,
4467,
4469,
4492,
4493,
4494,
4495,
4496,
4497,
4108,
4112,
4112,
4112,
4114,
4115,
4116,
4118,
4143,
4145,
4146,
4204,
4206,
4413,
4208,
4421,
4226,
4412,
4227,
4228,
4229,
4232,
4233,
4234,
4245,
4247,
4252,
4500,
4512,
4513,
4514,
4515,
4522,
4523,
4524,
4525,
4528,
4532,
4533,
4534,
4535,
4535,
4542,
4543,
4552,
4553,
4554,
4554,
4556,
4556,
4556,
4556,

4557,
4558,
4558,
4558,
4562,
4563,
4564,
4565,
4566,
4566,
4566,
4571,
4571,
4573,
4573,
4574,
4574,
4576,
4577,
4578,
4579,
4581,
4582,
4583,
4883,
4584,
4584,
4584,
4585,
4586,
4587,
4588,
4588,
2540,
2544,
2545,
3253,
3254,
3254,
3307,
4600,
4612,
4613,
4614,
4615,
4616,
4617,
4618,
4622,
4623,
4624,
4625,
4626,
4628,
4629,
4632,
4633,
4634,
4652,
4653,
4654,
4655,
4655,
4656,
4657,
4658,
5012,
5012,
5012,
5012,
5013,
5014,
5015,
5016,
5746,
4702,
4703,
4710,
4710,
4712,
4713,
4714,
4715,
4716,
4716,
4717,
4718,
4719,
1797,
2076,
2333,
2500,
2512,
2513,
2514,
2515,
2516,
2517,
2518,
2520,
2532,
2533,
2534,
2534,
2535,
2536,
2537,
2538,
2542,
2543,
2552,
2553,
2554,
2555,
2556,
2557,
2558,
2560,
2562,
2563,
2564,
2565,
2572,
2575,
2576,
2577,
3237,
2603,
2604,
2605,
2606,
2607,
2608,
2608,
2610,
2610,
2610,
2612,
2613,
2615,
2615,
2616,
2710,
2712,
2717,
2715,
2716,
2713,
2720,
2720,
2722,
2723,
2732,
2732,
2732,
2732,
2733,
2735,
2735,
2736,
2738,
2740,
2742,
2748,
2743,
2744,
2747,
2745,
2746,
2747,
2762,
3000,
3020,
3095,
3032,
3033,
3034,
3035,
3036,
3037,
3038,
3042,
3043,
3044,
3045,
3046,
3047,
3048,
3052,
3053,
3054,
3063,
3065,
3065,
3065,
3066,
3067,
3068,
3072,
3072,
3073,
3074,
3075,
3076,
3077,
3078,
3082,
3083,
3084,
3088,
3086,
3087,
3088,
3089,
3096,
3097,
3098,
3098,
3099,
3110,
3112,
3114,
3115,
3116,
3629,
3628,
3122,
3123,
3124,
3125,
3126,
3127,
3128,
3132,
3664,
3665,
3662,
3663,
3661,
3144,
3145,
3147,
3148,
3150,
3152,
3153,
3154,
3155,
3156,
3157,
3158,
3159,
3172,
3173,
3174,
3176,
3177,
3179,
3183,
3126,
3202,
3203,
3204,
3205,
3206,
3207,
3208,
3225,
3226,
3232,
3233,
3234,
3235,
3236,
3250,
3251,
3252,
3255,
3256,
3257,
3257,
3262,
3263,
3264,
3266,
3267,
3268,
3270,
3271,
3272,
3273,
3274,
3282,
3283,
3292,
3293,
3294,
3295,
3296,
3297,
3298,
3302,
3303,
3308,
3312,
3313,
3314,
3315,
3315,
3322,
3322,
3323,
3324,
3325,
3326,
3303,
3305,
3306,
3309,
3317,
3317,
3360,
3429,
3376,
3372,
3373,
3373,
3374,
3362,
3363,
3365,
3365,
3366,
3367,
3368,
3400,
3412,
3413,
3414,
3415,
3416,
3417,
3418,
3419,
3421,
3422,
3422,
3422,
3423,
3424,
3425,
3426,
3427,
3428,
3432,
3433,
3434,
3435,
3436,
3437,
3438,
3439,
3452,
3453,
3454,
3455,
3456,
3457,
3462,
3463,
3464,
3465,
3472,
3473,
3474,
3475,
3475,
3476,
3111,
3503,
3504,
3506,
3507,
3508,
3510,
3512,
3513,
3415,
3672,
3672,
3673,
3674,
3671,
3531,
3532,
3533,
3534,
3535,
3536,
3537,
3538,
3543,
3550,
3551,
3552,
3553,
3555,
3556,
3557,
3600,
3601,
3602,
3603,
3604,
3605,
3606,
3607,
3608,
3617,
3618,
3619,
3619,
3622,
3635,
3631,
3636,
3612,
3613,
3614,
3615,
3616,
3623,
3624,
3625,
3626,
3627,
3633,
3634,
3638,
3638,
3645,
3646,
3647,
3652,
3653,
3654,
3655,
3656,
3656,
3657,
3658,
3700,
3702,
3703,
3703,
3704,
3705,
3706,
3707,
3711,
3711,
3712,
3713,
3714,
3714,
3715,
3716,
3717,
3718,
3722,
3723,
3724,
3725,
3752,
3753,
3754,
3755,
3756,
3757,
3758,
3762,
3763,
3764,
3765,
3766,
3770,
3771,
3772,
3773,
3775,
3776,
3777,
3778,
3780,
3781,
3782,
3783,
3784,
3785,
3792,
3800,
3800,
3800,
3801,
3801,
3801,
3802,
3803,
3804,
3805,
3806,
3807,
3812,
3813,
3814,
3815,
3816,
3816,
3818,
3822,
3822,
3823,
3824,
3825,
3826,
3852,
3853,
3854,
3855,
3855,
3855,
3856,
3857,
3858,
3860,
3860,
3862,
3863,
3863,
3864,
4539,
4539,
4536,
4537,
4538,
3375,
4564,
4704,
3380,
3377,
4900,
4911,
4912,
4913,
4914,
4916,
4917,
4917,
4955,
4919,
4922,
4923,
4924,
4932,
4933,
4934,
4935,
4936,
4937,
4938,
4942,
4943,
4944,
4938,
4950,
4952,
4953,
4954,
3860,
6083,
6084,
6085,
6086,
6197,
3113,
3044,
6197,
3855,
3700,
3380,
1595,
2735,
2715,
2827,
2717,
2575,
2572,
2556,
3283,
4932,
3294,
3429,
3429,
3324,
3424,
3472,
3421,
3425,
2577,
3303,
3256,
3053,
3053,
3303,
3251,
3305,
3053,
3309,
3510,
3510,
3671,
3532,
3629,
3504,
3207,
3274,
3272,
3274,
3632,
3086,
3629,
3628,
3127,
3116,
3116,
3128,
3636,
3623,
3624,
3645,
3376,
3366,
3367,
4704,
2735,
3656,
4922,
3757,
2715,
2500,
3415,
3415,
3044,
3800,
2575


	
	);
	
	$arrayBasel=array(
4000,
4001,
4002,
4003,
4004,
4005,
4006,
4007,
4008,
4009,
4010,
4011,
4012,
4013,
4014,
4015,
4016,
4017,
4018,
4019,
4020,
4021,
4022,
4023,
4024,
4025,
4026,
4027,
4028,
4029,
4030,
4031,
4032,
4033,
4034,
4035,
4036,
4037,
4038,
4039,
4040,
4041,
4042,
4043,
4044,
4045,
4046,
4047,
4048,
4049,
4050,
4051,
4052,
4053,
4054,
4055,
4056,
4057,
4058,
4059,
4060,
4061,
4062,
4063,
4064,
4065,
4066,
4067,
4068,
4069,
4070,
4071,
4072,
4073,
4074,
4075,
4076,
4077,
4078,
4079,
4080,
4081,
4082,
4083,
4084,
4085,
4086,
4087,
4088,
4089,
4090,
4091,
4092,
4093,
4094,
4095,
4096,
4097,
4098,
4099,
4100,
4101,
4102,
4103,
4104,
4105,
4106,
4107,
4108,
4109,
4110,
4111,
4112,
4113,
4114,
4115,
4116,
4117,
4118,
4119,
4120,
4121,
4122,
4123,
4124,
4125,
4126,
4127,
4128,
4129,
4130,
4131,
4132,
4133,
4134,
4135,
4136,
4137,
4138,
4139,
4140,
4141,
4142,
4143,
4144,
4145,
4146,
4147,
4148,
4149,
4150,
4151,
4152,
4153,
4154,
4155,
4156,
4157,
4158,
4159,
4160,
4161,
4162,
4163,
4164,
4165,
4166,
4167,
4168,
4169,
4170,
4171,
4172,
4173,
4174,
4175,
4176,
4177,
4178,
4179,
4180,
4181,
4182,
4183,
4184,
4185,
4186,
4187,
4188,
4189,
4190,
4191,
4192,
4193,
4194,
4195,
4196,
4197,
4198,
4199,
4200,
4201,
4202,
4203,
4204,
4205,
4206,
4207,
4208,
4209,
4210,
4211,
4212,
4213,
4214,
4215,
4216,
4217,
4218,
4219,
4220,
4221,
4222,
4223,
4224,
4225,
4226,
4227,
4228,
4229,
4230,
4231,
4232,
4233,
4234,
4235,
4236,
4237,
4238,
4239,
4240,
4241,
4242,
4243,
4244,
4245,
4246,
4247,
4248,
4249,
4250,
4251,
4252,
4253,
4254,
4255,
4256,
4257,
4258,
4259,
4260,
4261,
4262,
4263,
4264,
4265,
4266,
4267,
4268,
4269,
4270,
4271,
4272,
4273,
4274,
4275,
4276,
4277,
4278,
4279,
4280,
4281,
4282,
4283,
4284,
4285,
4286,
4287,
4288,
4289,
4290,
4291,
4292,
4293,
4294,
4295,
4296,
4297,
4298,
4299,
4300,
4301,
4302,
4303,
4304,
4305,
4306,
4307,
4308,
4309,
4310,
4311,
4312,
4313,
4314,
4315,
4316,
4317,
4318,
4319,
4320,
4321,
4322,
4323,
4324,
4325,
4326,
4327,
4328,
4329,
4330,
4331,
4332,
4333,
4334,
4335,
4336,
4337,
4338,
4339,
4340,
4341,
4342,
4343,
4344,
4345,
4346,
4347,
4348,
4349,
4350,
4351,
4352,
4353,
4354,
4355,
4356,
4357,
4358,
4359,
4360,
4361,
4362,
4363,
4364,
4365,
4366,
4367,
4368,
4369,
4370,
4371,
4372,
4373,
4374,
4375,
4376,
4377,
4378,
4379,
4380,
4381,
4382,
4383,
4384,
4385,
4386,
4387,
4388,
4389,
4390,
4391,
4392,
4393,
4394,
4395,
4396,
4397,
4398,
4399,
4400,
4401,
4402,
4403,
4404,
4405,
4406,
4407,
4408,
4409,
4410,
4411,
4412,
4413,
4414,
4415,
4416,
4417,
4418,
4419,
4420,
4421,
4422,
4423,
4424,
4425,
4426,
4427,
4428,
4429,
4430,
4431,
4432,
4433,
4434,
4435,
4436,
4437,
4438,
4439,
4440,
4441,
4442,
4443,
4444,
4445,
4446,
4447,
4448,
4449,
4450,
4451,
4452,
4453,
4454,
4455,
4456,
4457,
4458,
4459,
4460,
4461,
4462,
4463,
4464,
4465,
4466,
4467,
4468,
4469,
4470,
4471,
4472,
4473,
4474,
4475,
4476,
4477,
4478,
4479,
4480,
4481,
4482,
4483,
4484,
4485,
4486,
4487,
4488,
4489,
4490,
4491,
4492,
4493,
4494,
4495,
4496,
4497,
4498,
4499

);
	/*Funktion S&auml;ubern des Herrlibergarrays
	$arrayHerzogEnd=array();
	foreach ($arrayHerzog as $key => $value) {

		if (in_array($value, $arrayWohlen) == 0) {
		echo $value.",<br>";
		}
	}
	print_r($arrayHerzogEnd);
	*/
	
	if (substr($plz,0,2) == "34" or substr($plz,0,2) == "35") {
		$outSitz="Hasle R&uuml;egsau";
		return "3415-Hasle_A.jpg";
	} else {
		if (in_array($plz,$arrayHerrli)) {
			$outSitz="Herrliberg";
			return "A-Post_Herrliberg.jpg";
		} elseif (in_array($plz,$arrayBasel)) {
			$outSitz="Basel";
			return "A-Post_Herzogenbuchsee.jpg";
		} else {
			if (in_array($plz,$arrayHerzog)) {
			$outSitz="Herzogenbuchsee";
			return  "A-Post_Herzogenbuchsee.jpg";
			} else {
				if (in_array($plz,$arrayWohlen)) {
				$outSitz="Wohlen";
				return  "A-Post_Wohlen.jpg";
				} else {
				$outSitz="Herrliberg";
				return "A-Post_Herrliberg.jpg";
				}
			}
		}
	}
}
 ?>