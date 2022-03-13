<?php

use yii\helpers\Html;
use yii\widgets\Menu;
use yii\helpers\BaseUrl;
use app\helpers\AppHelper;

$controller = $this->context->action->controller->id;
$method = $this->context->action->id;
$current_action = $controller . '/' . $method;
$get = Yii::$app->request->queryParams;
$ctype = '';
if ($controller == 'category' && isset($get['type'])) {
    $ctype = $get['type'];
}
?>
<style>
    .current_page {
        background-color: #4FB3CD;
    }

    #side-menu .items a:hover {
        text-transform: none;
        background-color: #FF7C1C !important;
    }

    #side-menu li a {
        text-transform: none;
    }
</style>

<aside id="menu">
    <div id="navigation">
        <div class="profile-picture">
            <?php
            if (
                !empty(Yii::$app->user->identity)
                && Yii::$app->user->identity->image != ""
                && file_exists(Yii::getAlias('@webroot') . '/uploads/' . Yii::$app->user->identity->image)
            ) {
            ?>
                <a href="#">
                    <img src="<?php echo BaseUrl::home() ?>uploads/<?php echo Yii::$app->user->identity->image ?>" class="img-circle m-b" alt="logo" style="width: 76px;">
                </a>
            <?php
            }
            ?>

            <div class="stats-label text-color">
                <span class="font-extra-bold font-uppercase">
                    <?php
                    //echo "<pre>";print_r(Yii::$app->user);die;
                    if (\Yii::$app->session['_eyadatAuth'] == 1) {
                        echo ucfirst(Yii::$app->user->identity->name);
                        echo '<p style="font-size:10px;">Admin</p>';
                    } elseif (\Yii::$app->session['_eyadatAuth'] == 2) {
                        echo ucfirst(Yii::$app->user->identity->name_en);
                        echo '<p style="font-size:10px;">Clinic</p>';
                    } elseif (\Yii::$app->session['_eyadatAuth'] == 3) {
                        echo ucfirst(Yii::$app->user->identity->name_en);
                        echo '<p style="font-size:10px;">Doctor</p>';
                    } elseif (\Yii::$app->session['_eyadatAuth'] == 4) {
                        echo ucfirst(Yii::$app->user->identity->name_en);
                        echo '<p style="font-size:10px;">Lab</p>';
                    } elseif (\Yii::$app->session['_eyadatAuth'] == 5) {
                        echo ucfirst(Yii::$app->user->identity->name_en);
                        echo '<p style="font-size:10px;">Pharmacy</p>';
                    } elseif (\Yii::$app->session['_eyadatAuth'] == 8) {
                        echo ucfirst(Yii::$app->user->identity->name_en);
                        echo '<p style="font-size:10px;">Translator</p>';
                    }
                    ?>
                </span>
                <div class="dropdown">
                    <a class="dropdown-toggle" href="#" data-toggle="dropdown">
                        <small class="text-muted">
                            <?php
                            $profile_auth = 'profile/edit';
                            if (Yii::$app->session['_eyadatAuth'] == 2) {
                                $profile_auth = 'clinic/profile';
                            } elseif (Yii::$app->session['_eyadatAuth'] == 3) {
                                $profile_auth = 'doctor/profile';
                            } elseif (Yii::$app->session['_eyadatAuth'] == 4) {
                                $profile_auth = 'lab/profile';
                            } elseif (Yii::$app->session['_eyadatAuth'] == 5) {
                                $profile_auth = 'pharmacies/profile';
                            }
                            $editUrl = BaseUrl::home() . $profile_auth;
                            ?>
                            <b class="caret"></b>
                        </small>
                    </a>
                    <ul class="dropdown-menu animated flipInX m-t-xs">
                        <li><a href="<?php echo $editUrl ?>">Profile</a></li>
                        <li class="divider"></li>
                        <li>
                            <?= Html::beginForm(['/site/logout'], 'post') ?>
                            <?= Html::submitButton('Logout', ['class' => 'logout-btn']) ?>
                            <?= Html::endForm() ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <?php
        echo Menu::widget([
            'encodeLabels' => false,
            'items' => [
                [
                    'label' => '<span class="nav-label">Dashboard</span>',
                    'active' => ($current_action == 'dashboard/index') ? true : "",
                    'url' => ['/'],
                ],
                [
                    'label' => '<span class="nav-label">Appointments</span> <span class="fa arrow"></span>',
                    'dropDownOptions' => [
                        'class' => 'nav nav-second-level'
                    ],
                    'active' => ($controller == 'doctor-appointment') || ($controller == 'lab-appointment') ? true : "",
                    'template' => '<a href="#" >{label}</a>',
                    'items' => [
                        [
                            'label' => '<span class="nav-label">Doctor Appointments</span> <span class="fa arrow"></span>',
                            'dropDownOptions' => [
                                'class' => 'nav nav-second-level'
                            ],
                            'active' => (($controller == 'doctor') || ($controller == 'category' && $ctype == 'D') || ($controller == 'doctor-appointment')) ? true : "",
                            'template' => '<a href="#" >{label}</a>',
                            'items' => [
                                [
                                    'label' => 'Upcoming <span class="label label-success pull-right" title="">' . AppHelper::totalDoctorAppointment('U') . '</span>',
                                    'url' => ['doctor-appointment/index', 'DoctorAppointmentSearch[type]' => 'U'],
                                    'active' => ($controller == 'doctor-appointment') ? true : "",
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                                ],
                                [
                                    'label' => 'Completed <span class="label label-success pull-right" title="">' . AppHelper::totalDoctorAppointment('C') . '</span>',
                                    'url' => ['doctor-appointment/index', 'DoctorAppointmentSearch[type]' => 'C'],
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                                ],
                                [
                                    'label' => 'No Show <span class="label label-success pull-right" title="No Show">' . AppHelper::totalDoctorAppointment('N') . '</span>',
                                    'url' => ['doctor-appointment/index', 'DoctorAppointmentSearch[type]' => 'N'],
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                                ],
                                [
                                    'label' => 'Failed <span class="label label-success pull-right" title="">' . AppHelper::totalDoctorAppointment('F') . '</span>',
                                    'url' => ['doctor-appointment/index', 'DoctorAppointmentSearch[type]' => 'F'],
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                                ],
                            ],
                        ],
                        [
                            'label' => '<span class="nav-label">Lab Appointments</span> <span class="fa arrow"></span>',
                            'dropDownOptions' => [
                                'class' => 'nav nav-second-level'
                            ],
                            'active' => (($controller == 'lab-appointment') || ($controller == 'category' && $ctype == 'L') || ($controller == 'lab-appointment')) ? true : "",
                            'template' => '<a href="#" >{label}</a>',
                            'items' => [
                                [
                                    'label' => 'Upcoming  <span class="label label-success pull-right" title="">' . AppHelper::totalLabAppointment('U') . '</span>',
                                    'url' => ['lab-appointment/index', 'LabAppointmentSearch[atype]' => 'U'],
                                    'active' => ($controller == 'lab-appointment') ? true : "",
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                                ],
                                [
                                    'label' => 'Completed  <span class="label label-success pull-right" title="">' . AppHelper::totalLabAppointment('C') . '</span>',
                                    'url' => ['lab-appointment/index', 'LabAppointmentSearch[atype]' => 'C'],
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                                ],
                                [
                                    'label' => 'No Show <span class="label label-success pull-right" title="">' . AppHelper::totalLabAppointment('N') . '</span>',
                                    'url' => ['lab-appointment/index', 'LabAppointmentSearch[atype]' => 'N'],
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                                ],
                                [
                                    'label' => 'Failed  <span class="label label-success pull-right" title="">' . AppHelper::totalLabAppointment('F') . '</span>',
                                    'url' => ['lab-appointment/index', 'LabAppointmentSearch[atype]' => 'F'],
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                                ],
                            ],
                        ],

                    ],
                ],
                [

                    'label' => '<span class="nav-label">Appointments</span> <span class="fa arrow"></span>',
                    'dropDownOptions' => [
                        'class' => 'nav nav-second-level'
                    ],
                    'active' => (($ctype == 'D') || ($controller == 'doctor-appointment')) ? true : "",
                    'template' => '<a href="#" >{label}</a>',
                    'items' => [
                        [
                            'label' => 'Upcoming <span class="label label-success pull-right" title="">' .  AppHelper::totalDoctorAppointment('U') . '</span>',
                            'url' => ['doctor-appointment/index', 'DoctorAppointmentSearch[type]' => 'U'],
                            'active' => ($controller == 'doctor-appointment') ? true : "",
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 2 || Yii::$app->session['_eyadatAuth'] == 3 || Yii::$app->session['_eyadatAuth'] == 8) ? true : false,
                        ],
                        [
                            'label' => 'Completed <span class="label label-success pull-right" title="">' . AppHelper::totalDoctorAppointment('C') . '</span>',
                            'url' => ['doctor-appointment/index', 'DoctorAppointmentSearch[type]' => 'C'],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 2 || Yii::$app->session['_eyadatAuth'] == 3 || Yii::$app->session['_eyadatAuth'] == 8) ? true : false,
                        ],
                        [
                            'label' => 'No Show <span class="label label-success pull-right" title="No Show">' . AppHelper::totalDoctorAppointment('N') . '</span>',
                            'url' => ['doctor-appointment/index', 'DoctorAppointmentSearch[type]' => 'N'],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 2 || Yii::$app->session['_eyadatAuth'] == 3 || Yii::$app->session['_eyadatAuth'] == 8) ? true : false,
                        ],
                        [
                            'label' => 'Failed <span class="label label-success pull-right" title="">' . AppHelper::totalDoctorAppointment('F') . '</span>',
                            'url' => ['doctor-appointment/index', 'DoctorAppointmentSearch[type]' => 'F'],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 2 || Yii::$app->session['_eyadatAuth'] == 3 || Yii::$app->session['_eyadatAuth'] == 8) ? true : false,
                        ],
                    ],
                ],

                [
                    'label' => '<span class="nav-label">Appointments</span> <span class="fa arrow"></span>',
                    'dropDownOptions' => [
                        'class' => 'nav nav-second-level'
                    ],
                    'active' => (($controller == 'lab-appointment') || ($controller == 'category' && $ctype == 'L') || ($controller == 'lab-appointment')) ? true : "",
                    'template' => '<a href="#" >{label}</a>',
                    'items' => [
                        [
                            'label' => 'Upcoming <span class="label label-success pull-right" title="">' . AppHelper::totalLabAppointment('U') . '</span>',
                            'url' => ['lab-appointment/index', 'LabAppointmentSearch[atype]' => 'U'],
                            'active' => ($controller == 'lab-appointment') ? true : "",
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 4) ? true : false,
                        ],
                        [
                            'label' => 'Completed <span class="label label-success pull-right" title="">' . AppHelper::totalLabAppointment('C') . '</span>',
                            'url' => ['lab-appointment/index', 'LabAppointmentSearch[atype]' => 'C'],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 4) ? true : false,
                        ],
                        [
                            'label' => 'No Show <span class="label label-success pull-right" title="">' . AppHelper::totalLabAppointment('N') . '</span>',
                            'url' => ['lab-appointment/index', 'LabAppointmentSearch[atype]' => 'N'],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 4) ? true : false,
                        ],
                        [
                            'label' => 'Failed <span class="label label-success pull-right" title="">' . AppHelper::totalLabAppointment('F') . '</span>',
                            'url' => ['lab-appointment/index', 'LabAppointmentSearch[atype]' => 'F'],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 4) ? true : false,
                        ],
                    ],
                ],

                [
                    'label' => '<span class="nav-label">Orders</span> <span class="fa arrow"></span>',
                    'dropDownOptions' => [
                        'class' => 'nav nav-second-level'
                    ],
                    'active' => ($controller == 'order') ? true : "",
                    'template' => '<a href="#" >{label}</a>',
                    'items' => [
                        [
                            'label' => 'All Orders',
                            'url' => ['order/index'],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 1 || Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/order/index'))
                        ],
                        [
                            'label' => 'Pending Orders <span class="label label-success pull-right" title="Pending order">' . AppHelper::totalOrderStatus(1) . '</span>',
                            'url' => ['order/index', 'OrdersSearch[status_id]' => 1],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                        ],
                        [
                            'label' => 'Accepted Orders <span class="label label-success pull-right" title="Pending order">' . AppHelper::totalOrderStatus(2) . '</span>',
                            'url' => ['order/index', 'OrdersSearch[status_id]' => 2],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                        ],
                        [
                            'label' => 'In Progress Orders <span class="label label-success pull-right" title="Pending order">' . AppHelper::totalOrderStatus(3) . '</span>',
                            'url' => ['order/index', 'OrdersSearch[status_id]' => 3],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                        ],
                        [
                            'label' => 'Ready for Delivery Orders <span class="label label-success pull-right" title="Pending order">' . AppHelper::totalOrderStatus(7) . '</span>',
                            'url' => ['order/index', 'OrdersSearch[status_id]' => 7],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? false : false,
                        ],
                        [
                            'label' => 'Ready For Delivery Orders <span class="label label-success pull-right" title="Pending order">' . AppHelper::totalOrderStatus(8) . '</span>',
                            'url' => ['order/index', 'OrdersSearch[status_id]' => 8],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                        ],
                        [
                            'label' => 'Out For Delivery Orders <span class="label label-success pull-right" title="Pending order">' . AppHelper::totalOrderStatus(4) . '</span>',
                            'url' => ['order/index', 'OrdersSearch[status_id]' => 4],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                        ],
                        [
                            'label' => 'Delivered Orders <span class="label label-success pull-right" title="Pending order">' . AppHelper::totalOrderStatus(5) . '</span>',
                            'url' => ['order/index', 'OrdersSearch[status_id]' => 5],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                        ],
                        [
                            'label' => 'Canceled Orders <span class="label label-success pull-right" title="Pending order">' . AppHelper::totalOrderStatus(6) . '</span>',
                            'url' => ['order/index', 'OrdersSearch[status_id]' => 6],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                        ],
                    ],
                ],
                [
                    'label' => '<span class="nav-label">Orders</span> <span class="fa arrow"></span>',
                    'dropDownOptions' => [
                        'class' => 'nav nav-second-level'
                    ],
                    'active' => ($controller == 'pharmacy-order') ? true : "",
                    'template' => '<a href="#" >{label}</a>',
                    'items' => [

                        [
                            'label' => 'All Orders <span class="label label-success pull-right" title="Pending order">' . AppHelper::totalPharmacyOrderStatus() . '</span>',
                            'url' => ['pharmacy-order/orders'],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 5) ? true : false,
                        ],
                        [
                            'label' => 'Accepted Orders <span class="label label-success pull-right" title="Pending order">' . AppHelper::totalPharmacyOrderStatus(1) . '</span>',
                            'url' => ['pharmacy-order/orders', 'OrdersSearch[status_id]' => 1],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 5) ? true : false,
                        ],
                        [
                            'label' => 'Ready for Pickup <span class="label label-success pull-right" title="Pending order">' . AppHelper::totalPharmacyOrderStatus(2) . '</span>',
                            'url' => ['pharmacy-order/orders', 'OrdersSearch[status_id]' => 2],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 5) ? true : false,
                        ],
                        [
                            'label' => 'Delivered By Driver <span class="label label-success pull-right" title="Pending order">' . AppHelper::totalPharmacyOrderStatus(4) . '</span>',
                            'url' => ['pharmacy-order/orders', 'OrdersSearch[status_id]' => 4],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 5) ? true : false,
                        ],
                    ],
                ],

                /*[
                        'label' => '<span class="nav-label">Pharmacy Orders</span> <span class="fa arrow"></span>',
                        'dropDownOptions' => [
                            'class' => 'nav nav-second-level'
                        ],
                        'active' => ($controller == 'order') ? true : "",
                        'template' => '<a href="#" >{label}</a>',
                        'items' => [
                            [
                                'label' => 'Ready for Pickup Assigned',
                                'url' => ['pharmacy/index'],
                                'visible' => (Yii::$app->session['_eyadatAuth'] == 1 || Yii::$app->session['_eyadatAuth'] == 5  || Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/order/index'))
                            ],
                            [
                                'label' => 'Ready for Pickup Unassigned',
                                'url' => ['pharmacy/index'],
                                'visible' => (Yii::$app->session['_eyadatAuth'] == 1 || Yii::$app->session['_eyadatAuth'] == 5  || Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/order/index'))
                            ],
                        ],
                    ],*/
                [
                    'label' => '<span class="nav-label">Delivery Management</span> <span class="fa arrow"></span>',
                    'dropDownOptions' => [
                        'class' => 'nav nav-second-level'
                    ],
                    'active' => ($controller == 'drivers' || $controller == 'pharmacy-order') ? true : "",
                    'template' => '<a href="#" >{label}</a>',
                    'items' => [
                        [
                            'label' => 'Ready for Pickup Orders <span class="label label-success pull-right" title="Pending order">' . AppHelper::totalReadyForDeliveryOrder(0) . '</span>',
                            'url' => ['pharmacy-order/index'],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 1)  ? true : false,
                        ],
                        [
                            'label' => 'Ready for Delivery Orders <span class="label label-success pull-right" title="Pending order">' . AppHelper::totalReadyForDeliveryOrder(1) . '</span>',
                            'url' => ['pharmacy-order/ready-for-delivery'],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 1)  ? true : false,
                        ],
                        [
                            'label' => 'Create Driver',
                            'url' => ['drivers/create'],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 1 || Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/order/index'))
                        ],
                        [
                            'label' => 'Manage Drivers',
                            'url' => ['drivers/index', 'DriversSearch[status_id]' => 1],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                        ],
                    ],
                ],
                [
                    'label' => '<span class="nav-label">Banners</span>',
                    'active' => ($current_action == 'banner/index') ? true : "",
                    'url' => ['banner/index'],
                    'visible' => Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/banner/create')
                ],


                [
                    'label' => '<span class="nav-label">Doctor Management</span> <span class="fa arrow"></span>',
                    'dropDownOptions' => [
                        'class' => 'nav nav-second-level'
                    ],
                    'active' => ($controller == 'doctor' || $controller == 'symptoms' || $controller == 'insurance' || $controller == 'clinic'  || $controller == 'hospital' || $ctype == 'D' || $ctype == 'C') ? true : "",
                    'template' => '<a href="#" >{label}</a>',
                    'items' => [
                        [
                            'label' => '<span class="nav-label">Symptoms</span>',
                            'active' => ($current_action == 'symptoms/index') ? true : "",
                            'url' => ['symptoms/index'],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                        ],
                        [
                            'label' => '<span class="nav-label">Insurance</span>',
                            'active' => ($current_action == 'insurance/index') ? true : "",
                            'url' => ['insurance/index'],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false
                        ],

                        [
                            'label' => '<span class="nav-label">Clinic</span> <span class="fa arrow"></span>',
                            'dropDownOptions' => [
                                'class' => 'nav nav-second-level'
                            ],
                            'active' => ($controller == 'clinic' || $ctype == 'C') ? true : "",
                            'template' => '<a href="#" >{label}</a>',
                            'items' => [
                                [
                                    'label' => 'Clinic Categories',
                                    'url' => ['category/index?type=C'],
                                    'active' => ($controller == 'category' && $ctype == 'C') ? true : "",
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                                ],
                                [
                                    'label' => 'Create Clinic',
                                    'url' => ['clinic/create'],
                                    'visible' => Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/clinic/create')
                                ],
                                [
                                    'label' => 'Manage Clinic',
                                    'url' => ['clinic/index'],
                                    'visible' => Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/clinic/index')
                                ],
                            ],
                        ],

                        [
                            'label' => '<span class="nav-label">Hospital</span> <span class="fa arrow"></span>',
                            'dropDownOptions' => [
                                'class' => 'nav nav-second-level'
                            ],
                            'active' => ($controller == 'hospital' || $ctype == 'H') ? true : "",
                            'template' => '<a href="#" >{label}</a>',
                            'items' => [
                                /*[
                                    'label' => 'Hospital Categories',
                                    'url' => ['category/index?type=C'],
                                    'active' => ($controller == 'category' && $ctype == 'C') ? true : "",
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                                ],*/
                                [
                                    'label' => 'Create Hospital',
                                    'url' => ['hospital/create'],
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                                ],
                                [
                                    'label' => 'Manage Hospital',
                                    'url' => ['hospital/index'],
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                                ],
                            ],
                        ],
                        [
                            'label' => '<span class="nav-label">Doctor</span> <span class="fa arrow"></span>',
                            'dropDownOptions' => [
                                'class' => 'nav nav-second-level'
                            ],
                            'active' => (($controller == 'doctor') || ($controller == 'category' && $ctype == 'D') || ($controller == 'doctor-appointment')) ? true : "",
                            'template' => '<a href="#" >{label}</a>',
                            'items' => [
                              
                                [
                                    'label' => 'Specialities',
                                    'url' => ['category/index?type=D'],
                                    'active' => ($controller == 'category') ? true : "",
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                                ],
                                [
                                    'label' => 'Create Doctor',
                                    'url' => ['doctor/create'],
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 2) || Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/doctor/create')
                                ],
                                [
                                    'label' => 'Manage Doctor',
                                    'url' => ['doctor/index'],
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 2) || Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/doctor/index')
                                ],
                            ],
                        ],
                        [
                            'label' => 'Translator',
                            'active' => ($current_action == 'promotions/index') ? true : "",
                            'url' => ['translator/index'],
                            'visible' => Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/promotions/index')
                        ],
                    ],
                ],
                [
                    'label' => '<span class="nav-label">Lab Management</span> <span class="fa arrow"></span>',
                    'dropDownOptions' => [
                        'class' => 'nav nav-second-level'
                    ],
                    'active' => ($controller == 'lab' || $controller == 'service' || $controller == 'tests' || $ctype == 'L' || $ctype == 'T' || $controller == 'lab-admins') ? true : "",
                    'template' => '<a href="#" >{label}</a>',
                    'items' => [
                        [
                            'label' => 'Services',
                            'url' => ['service/index'],
                            'active' => ($controller == 'lab') ? true : "",
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                        ],
                        [
                            'label' => '<span class="nav-label">Labs</span> <span class="fa arrow"></span>',
                            'dropDownOptions' => [
                                'class' => 'nav nav-second-level'
                            ],
                            'active' => ($controller == 'lab') ? true : "",
                            'template' => '<a href="#" >{label}</a>',
                            'items' => [
                                [
                                    'label' => 'Create Lab',
                                    'url' => ['lab/create'],
                                    'active' => ($controller == 'labs') ? true : "",
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                                ],
                                [
                                    'label' => 'Manage Labs',
                                    'url' => ['/lab/index'],
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 1) && Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/lab/index')
                                ],
                            ],
                        ],
                        [
                            'label' => 'Lab Admins',
                            'url' => ['lab-admins/index'],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                        ],
                        [
                            'label' => '<span class="nav-label">Tests</span> <span class="fa arrow"></span>',
                            'dropDownOptions' => [
                                'class' => 'nav nav-second-level'
                            ],
                            'active' => ($controller == 'tests' || $ctype == 'T') ? true : "",
                            'template' => '<a href="#" >{label}</a>',
                            'items' => [
                                [
                                    'label' => 'Test Category',
                                    'url' => ['category/index?type=T'],
                                    'active' => ($controller == 'tests') ? true : "",
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                                ],
                                [
                                    'label' => 'Create Test',
                                    'url' => ['tests/create'],
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 1) && Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/tests/create')
                                ],
                                [
                                    'label' => 'Manage Test',
                                    'url' => ['tests/index'],
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 1) && Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/tests/index')
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'label' => '<span class="nav-label">Pharmacy Management</span> <span class="fa arrow"></span>',
                    'dropDownOptions' => [
                        'class' => 'nav nav-second-level'
                    ],
                    'active' => (($controller == 'pharmacies' || $ctype == 'F') || ($controller == 'brand') || ($controller == 'category' && $ctype == 'P') || ($controller == 'attribute' || $controller == 'attribute-set' || $controller == 'manufacturer' || $controller == 'product')) ? true : "",
                    'template' => '<a href="#" >{label}</a>',
                    'items' => [
                        [
                            'label' => 'Categories',
                            'url' => ['category/index?type=F'],
                            'active' => ($controller == 'category') ? true : "",
                            'visible' => false, //(Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                        ],
                        [
                            'label' => '<span class="nav-label">Pharmacy</span> <span class="fa arrow"></span>',
                            'dropDownOptions' => [
                                'class' => 'nav nav-second-level'
                            ],
                            'active' => ($controller == 'pharmacies') ? true : "",
                            'template' => '<a href="#" >{label}</a>',
                            'items' => [
                                [
                                    'label' => 'Create Pharmacy',
                                    'url' => ['pharmacies/create'],
                                    'active' => ($controller == 'pharmacies') ? true : "",
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                                ],
                                [
                                    'label' => 'Manage Pharmacy',
                                    'url' => ['/pharmacies/index'],
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 1) && Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/pharmacies/index')
                                ],
                            ],
                        ],
                        [
                            'label' => 'Pharmacy Locations',
                            'url' => ['pharmacy-locations/index'],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? false : false,
                        ],
                        [
                            'label' => 'Pharmacy Admin',
                            'url' => ['pharmacy-admins/index'],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                        ],
                        [
                            'label' => '<span class="nav-label">Catalog</span> <span class="fa arrow"></span>',
                            'dropDownOptions' => [
                                'class' => 'nav nav-second-level'
                            ],
                            'active' => ($controller == 'product' || $controller == 'category' || $controller == 'attribute' || $controller == 'attribute-set' || $controller == 'brand' || $controller == 'best-sellers' || $controller == 'new-arrival' || $controller == 'trending' || $controller == 'manufacturer') ? true : "",
                            'template' => '<a href="#" >{label}</a>',
                            'items' => [
                                [
                                    'label' => 'Brands',
                                    'url' => ['/brand/index'],
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                                ],
                                [
                                    'label' => 'Categories',
                                    'url' => ['category/index?type=P'],
                                    'active' => ($controller == 'category' && $ctype == 'P') ? true : "",
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                                ],
                                [
                                    'label' => '<span class="nav-label">Attributes</span> <span class="fa arrow"></span>',
                                    'active' => ($controller == 'attribute' || $controller == 'attribute-set') ? true : "",
                                    'dropDownOptions' => [
                                        'class' => 'nav nav-second-level'
                                    ],
                                    'template' => '<a href="#" >{label}</a>',
                                    'items' => [
                                        [
                                            'label' => 'Manage Attributes',
                                            'url' => ['/attribute/index'],
                                            'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                                        ],
                                        [
                                            'label' => 'Manage Attribute Sets',
                                            'url' => ['/attribute-set/index'],
                                            'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                                        ],
                                    ],
                                ],
                                [
                                    'label' => 'Manufacturers',
                                    'url' => ['/manufacturer/index'],
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                                ],
                                [
                                    'label' => '<span class="nav-label">Products</span> <span class="fa arrow"></span>',
                                    'active' => (($controller == 'product' && ($method == "create" || $method == "index")) || ($controller == 'product' && $method == "single") || ($controller == 'product' && $method == "group") || $controller == 'best-sellers' || $controller == 'new-arrival' || $controller == 'trending') ? true : "",
                                    'dropDownOptions' => [
                                        'class' => 'nav nav-second-level'
                                    ],
                                    'template' => '<a href="#" >{label}</a>',
                                    'items' => [
                                        [
                                            'label' => 'Create new product',
                                            'url' => ['/product/create'],
                                            'visible' => (Yii::$app->session['_eyadatAuth'] == 1 || Yii::$app->session['_eyadatAuth'] == 5) ? true : false,
                                        ],
                                        [
                                            'label' => 'Manage Product',
                                            'url' => ['/product/index'],
                                            'visible' => (Yii::$app->session['_eyadatAuth'] == 1 || Yii::$app->session['_eyadatAuth'] == 5) ? true : false,
                                        ],
                                    ],
                                ],
                                [
                                    'label' => 'Product Import',
                                    'url' => ['/product/excel-import'],
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 1 || Yii::$app->session['_eyadatAuth'] == 5) ? true : false,
                                ],
                                [
                                    'label' => 'Bulk Image Upload',
                                    'url' => ['/product/bulk-upload'],
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 1 || Yii::$app->session['_eyadatAuth'] == 5) ? true : false,
                                ],
                            ]
                        ],
                    ],
                ],

                [
                    'label' => '<span class="nav-label">Promotions</span>',
                    'active' => ($current_action == 'promotions/index') ? true : "",
                    'url' => ['promotions/index'],
                    'visible' => Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/promotions/index')
                ],

                [
                    'label' => '<span class="nav-label">Help and Info</span> <span class="fa arrow"></span>',
                    'dropDownOptions' => [
                        'class' => 'nav nav-second-level'
                    ],
                    'active' => ($controller == 'faq') || ($controller == 'cms') || ($controller == 'feedback') || ($controller == 'source-message') ? true : "",
                    'template' => '<a href="#" >{label}</a>',
                    'items' => [
                        [
                            'label' => 'FAQ',
                            'url' => ['faq/index'],
                            'visible' => Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/faq/index')
                        ],
                        [
                            'label' => 'About us',
                            'url' => ['cms/view', 'id' => 1],
                            'visible' => Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/cms/view')
                        ],
                        [
                            'label' => 'Terms and conditions',
                            'url' => ['cms/view', 'id' => 2],
                            'visible' => Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/cms/view')
                        ],
                        [
                            'label' => 'Shipping Information',
                            'url' => ['cms/view', 'id' => 5],
                            'visible' => Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/cms/view')
                        ],
                        [
                            'label' => 'Return Policy & Procedures',
                            'url' => ['cms/view', 'id' => 3],
                            'visible' => Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/cms/view')
                        ],
                        [
                            'label' => 'Privacy Policy',
                            'url' => ['cms/view', 'id' => 4],
                            'visible' => Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/cms/view')
                        ],
                    ],
                ],
                [
                    'label' => '<span class="nav-label">Users</span> <span class="fa arrow"></span>',
                    'dropDownOptions' => [
                        'class' => 'nav nav-second-level'
                    ],
                    'active' => ($controller == 'admin') || ($controller == 'user') ? true : "",
                    'template' => '<a href="#" >{label}</a>',
                    'items' => [
                        [
                            'label' => 'Admins',
                            'url' => ['admin/index'],
                            'visible' => Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/admin/index')
                        ],
                        [
                            'label' => 'Customers',
                            'url' => ['user/index'],
                            'visible' => Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/user/index')
                        ],
                        [
                            'label' => 'User Family',
                            'url' => ['kids/index'],
                            'visible' => false,
                        ],
                    ],
                ],
                [
                    'label' => '<span class="nav-label">Settings</span> <span class="fa arrow"></span>',
                    'dropDownOptions' => [
                        'class' => 'nav nav-second-level'
                    ],
                    'active' => ($controller == 'store') || ($controller == 'currency') || ($controller == 'settings') || ($controller == 'country' || $controller == 'state' || $controller == 'area' || $controller == 'sector') ? true : "",
                    'template' => '<a href="#" >{label}</a>',
                    'items' => [
                        [
                            'label' => '<span class="nav-label">Currency</span>',
                            'active' => ($current_action == 'currency/index') ? true : "",
                            'url' => ['/currency/index'],
                            'visible' => Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/currency/index')
                        ],
                        [
                            'label' => '<span class="nav-label">Stores</span>',
                            'active' => ($current_action == 'store/index') ? true : "",
                            'url' => ['/store/index'],
                            'visible' => Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/store/index')
                        ],
                        [
                            'label' => '<span class="nav-label">Shipping Areas</span> <span class="fa arrow"></span>',
                            'active' => ($controller == 'country' || $controller == 'state' || $controller == 'area' || $controller == 'sector') ? true : "",
                            'dropDownOptions' => [
                                'class' => 'nav nav-second-level'
                            ],
                            'template' => '<a href="#" >{label}</a>',
                            'items' => [
                                [
                                    'label' => 'Countries',
                                    'url' => ['/country/index'],
                                    'visible' => Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/country/index')
                                ],
                                [
                                    'label' => 'States',
                                    'url' => ['state/index'],
                                    'visible' => Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/state/index')
                                ],
                                [
                                    'label' => 'Areas',
                                    'url' => ['area/index'],
                                    'visible' => Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/area/index')
                                ],
                                [
                                    'label' => 'Blocks',
                                    'url' => ['sector/index'],
                                    'visible' => Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/sector/index')
                                ],
                            ],
                        ],
                        [
                            'label' => 'General Settings',
                            'active' => ($current_action == 'settings/update') ? true : "",
                            'url' => ['/settings/update'],
                            'visible' => Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/settings/update')
                        ],
                    ],
                ],
                [
                    'label' => '<span class="nav-label">Reports</span> <span class="fa arrow"></span>',
                    'dropDownOptions' => [
                        'class' => 'nav nav-second-level'
                    ],
                    'active' => ($controller == 'report') || ($controller == 'payments-report') || ($controller == 'payment') || ($controller == 'lab-appointment-report') || ($controller == 'commission-report') ? true : "",
                    'template' => '<a href="#" >{label}</a>',
                    'items' => [
                        [
                            'label' => 'Payment Report',
                            'url' => ['payment/index'],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 1) || Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/payment/index')
                        ],
                        [
                            'label' => 'Pharmacy Sales Report',
                            'url' => ['payment/pharmacy-sale-report'],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 1) || Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/payment/index')
                        ],
                        [
                            'label' => 'Doctor Appointment Report',
                            'url' => ['report/doctor-appointment'],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ||  (Yii::$app->session['_eyadatAuth'] == 3 || Yii::$app->session['_eyadatAuth'] == 2) || Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/doctor-appointment-report/index')
                        ],
                        [
                            'label' => 'Lab Appointment Report',
                            'url' => ['report/lab-appointment'],
                            'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ||  (Yii::$app->session['_eyadatAuth'] == 4) || Yii::$app->auth->checkAccess(\Yii::$app->session['_eyadatUserRole'], '/lab-appointment-report/index')
                        ],
                        [
                            'label' => '<span class="nav-label">Commission Report</span> <span class="fa arrow"></span>',
                            'active' => ($controller == 'payment') ? true : "",
                            'dropDownOptions' => [
                                'class' => 'nav nav-second-level'
                            ],
                            'template' => '<a href="#" >{label}</a>',
                            'items' => [
                                [
                                    'label' => 'Pharmacy',
                                    'url' => ['payment/pharmacy-commission-report'],
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                                ],
                                [
                                    'label' => 'Clinic',
                                    'url' => ['payment/clinic-commission-report'],
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 2 || Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                                ],
                                [
                                    'label' => 'Labs',
                                    'url' => ['payment/lab-commission-report'],
                                    'visible' => (Yii::$app->session['_eyadatAuth'] == 1) ? true : false,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'itemOptions' => array('class' => 'items'),

            'activateParents' => true,
            "activeCssClass" => "current_page",
            'submenuTemplate' => "\n<ul class='nav nav-second-level'>\n{items}\n</ul>\n",
            'options' => ['class' => 'nav', 'id' => 'side-menu'],
        ]);
        ?>
    </div>
</aside>