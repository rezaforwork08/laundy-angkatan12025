<?php

if (empty($_SESSION['click_count'])) {
    $_SESSION['click_count'] = 0;
}
if (isset($_POST['save'])) {
    $trans_code = $_POST['trans_code'];
    $order_date = $_POST['order_date'];
    $id_customer = $_POST['id_customer'];
    $order_end_date = $_POST['order_end_date'];

    $insert = mysqli_query($koneksi, "INSERT INTO trans_order (`id_customer`, `trans_code`, `order_date`, `order_end_date`) VALUES ('$id_customer','$trans_code','$order_date','$order_end_date')");

    $id_order = mysqli_insert_id($koneksi);
    $qty = isset($_POST['qty']) ? $_POST['qty'] : [];
    $notes = isset($_POST['notes']) ? $_POST['notes'] : [];
    $service_name = isset($_POST['service_name']) ? $_POST['service_name'] : [];
    $subtotal = $_POST['subtotal'] ? $_POST['subtotal'] : [];

    $total = 0;
    for ($i = 0; $i < $_POST['countDispaly']; $i++) {
        $service = $service_name[$i];
        $cariId_service = mysqli_query($koneksi, "SELECT id FROM services WHERE service_name LIKE '%$service%'");
        $rowid_service = mysqli_fetch_assoc($cariId_service);
        $id_service = $rowid_service['id'];

        $qty_value = $qty[$i];
        $subtotal_value = $subtotal[$i];
        $notes_value = $notes[$i];

        // echo $id_order . "-" . $qty_value . " - " . $subtotal_value . " - " . $notes_value;
        $instOrderDet = mysqli_query($koneksi, "INSERT INTO trans_order_detail (id_order, id_service, qty, subtotal, notes) VALUES ('$id_order', '$id_service', '$qty_value', '$subtotal_value', '$notes_value')");

        $total += ($subtotal_value * $qty_value);
    }
    $update = mysqli_query($koneksi, "UPDATE trans_order SET total='$total' WHERE id = '$id_order'");
}

$id = isset($_GET['edit']) ? $_GET['edit'] : '';
$queryEdit = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$id'");
$rowEdit = mysqli_fetch_assoc($queryEdit);

if (isset($_POST['edit'])) {
    $id = $_GET['edit'];
    $$id_level = $_POST['id_level'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    if ($_POST['password']) {
        $password = sha1($_POST['password']);
    } else {
        $password = $rowEdit['password'];
    }

    $update = mysqli_query($koneksi, "UPDATE users 
    SET id_level ='$id_level', name='$name', email='$email', password='$password' WHERE id ='$id'");
    if ($update) {
        header("location:?page=user&update=success");
    }
}

$queryCustomers = mysqli_query($koneksi, "SELECT * FROM customers ORDER BY id DESC");
$rowCustomers  = mysqli_fetch_all($queryCustomers, MYSQLI_ASSOC);

$queryServices = mysqli_query($koneksi, "SELECT * FROM services ORDER BY id DESC");
$rowServices  = mysqli_fetch_all($queryServices, MYSQLI_ASSOC);

// TR032125001
$queryTrans = mysqli_query($koneksi, "SELECT max(id) as id_trans FROM trans_order");
$rowTrans = mysqli_fetch_assoc($queryTrans);

$id_trans = $rowTrans['id_trans']; //undifined error array key[id]
$id_trans++;

$kode_transaksi = "TR" . date("mdy") . sprintf("%03s", $id_trans);


?>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h3><?php echo isset($_GET['edit']) ? 'Edit' : 'Create New' ?> Trans Order</h3>
            </div>
            <div class="card-body mt-3">
                <form action="" method="post">
                    <input type="hidden" id="service_price">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3 row">
                                <div class="col-sm-3">
                                    <label for="">Transaction Code</label>
                                </div>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control"
                                        name="trans_code"
                                        readonly value="<?php echo $kode_transaksi ?>">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <div class="col-sm-3">
                                    <label for="">Order Date</label>
                                </div>
                                <div class="col-sm-5">
                                    <input type="date" class="form-control"
                                        name="order_date">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <div class="col-sm-3">
                                    <label for="">Service</label>
                                </div>
                                <div class="col-sm-5">
                                    <select name="" id="id_service" class="form-control">
                                        <option value="0">Choose Service</option>
                                        <?php foreach ($rowServices as $rowService): ?>
                                            <option value="<?php echo $rowService['id'] ?>" data-price="<?php echo $rowService['service_price'] ?>"><?php echo $rowService['service_name'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3 row">
                                <div class="col-sm-4">
                                    <label for="">Customer Name</label>
                                </div>
                                <div class="col-sm-8">
                                    <select name="id_customer" id="" class="form-control">
                                        <option value="">Choose Customer</option>
                                        <?php foreach ($rowCustomers as $rowCustomer): ?>
                                            <option value="<?php echo $rowCustomer['id'] ?>"><?php echo $rowCustomer['customer_name'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <div class="col-sm-4">
                                    <label for="">Pickup Date</label>
                                </div>
                                <div class="col-sm-5">
                                    <input type="date" class="form-control"
                                        name="order_end_date">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-sm-12">
                            <div align="right" class="mb-3">
                                <button type="button" class="btn btn-success btn-sm add-row">Add Row</button>
                                <input type="number" name="countDispaly" id="countDispaly" value="<?php echo $_SESSION['click_count'] ?>" readonly>
                            </div>
                            <table class="table table-bordered table-order">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Service</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Notes</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-primary" type="submit" name="<?php echo isset($_GET['edit']) ? 'edit' : 'save' ?>">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>