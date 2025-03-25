<?php
if (isset($_GET['idPay'])) {
    $id = $_GET['idPay'];
    $selectPay = mysqli_query($koneksi, "SELECT trans_order.*, trans_order.id AS troid, customers.*, customers.id AS cid FROM trans_order LEFT JOIN customers ON trans_order.id_customer = customers.id WHERE trans_order.id = $id");
    $row = mysqli_fetch_assoc($selectPay);
}
?>
<div class="container">
    <div class="row">
        <div class="col-1"></div>
        <div class="col-10">
            <div class="card">
                <div class="card-header text-center">
                    <div class="card-title">
                        Payment
                    </div>
                </div>
                <div class="card-body">
                    <div class="m-2">
                        <label for="" class="form-label">Customer Name</label>
                        <input type="text" class="form-control" value="<?php echo $row['customer_name'] ?>" readonly>
                    </div>
                    <form action="" method="post">
                        <div class="m-2">
                            <label for="" class="form-label">Order Status</label><br>
                            <input type="radio" name="status" value="0" <?php echo isset($row['status']) && $row['status'] == 0 ? 'checked' : '' ?>> Transaksi Sudah Selesai<br>
                            <input type="radio" name="status" value="1" <?php echo isset($row['status']) && $row['status'] == 1 ? 'checked' : '' ?>> Status Sudah di ambil
                        </div>
                        <div class="m-2">
                            <label for="" class="form-label">Order Pay</label>
                            <input type="number" name="pay" id="pay" oninput="payment()" class="form-control">
                        </div>
                        <div class="m-2">
                            <label for="">Change Pay</label>
                            <input type="number" name="change_pay" id="change_pay" class="form-control" readonly>
                        </div>
                        <div class="m-2">
                            <label for="">Total</label>
                            <input type="number" name="total" id="total" class="form-control" value="<?php echo $row['total'] ?>" readonly>
                        </div>
                        <div class="m-2">
                            <button class="btn btn-primary" type="submit" name="bayar">Bayar</button>
                            <a href="?page=trans-order" class="btn btn-secondary">Back</a>
                            <?php
                            if ($row['pay'] != 0) {
                            ?>
                                <a href="" class="btn btn-danger btn-sm">Print</a>
                            <?php
                            }
                            ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-1"></div>
    </div>
</div>
<script>
    function payment() {
        let pay = document.getElementById("pay").value;
        let total = document.getElementById("total").value;
        let hitung = pay - total;
        document.getElementById("change_pay").value = hitung;
    }
</script>