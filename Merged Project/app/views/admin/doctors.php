<h1>Manage doctors</h1>
<p class="muted">Add a doctor, edit their details, or remove one who has no appointments.</p>

<?php if ($error != ""): ?><div class="alert-error"><?php echo $error; ?></div><?php endif; ?>
<?php if ($success != ""): ?><div class="alert-ok"><?php echo $success; ?></div><?php endif; ?>

<div class="card">
    <div class="nav-label">Add a new doctor</div>

    <form method="post" action="<?php echo BASE_URL; ?>/index.php?url=admin/doctors">
        <input type="hidden" name="action" value="add">

        <label for="full_name">Full name</label>
        <input type="text" id="full_name" name="full_name" placeholder="Dr. Farhana Yasmin">

        <label for="email">Email</label>
        <input type="text" id="email" name="email">

        <label for="phone">Phone</label>
        <input type="text" id="phone" name="phone">

        <label for="dept_id">Department</label>
        <select id="dept_id" name="dept_id">
            <option value="">-- Select --</option>
            <?php foreach ($departments as $d): ?>
            <option value="<?php echo $d["dept_id"]; ?>"><?php echo $d["dept_name"]; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="specialization">Specialization</label>
        <input type="text" id="specialization" name="specialization">

        <label for="consultation_fee">Consultation fee (Tk)</label>
        <input type="text" id="consultation_fee" name="consultation_fee" value="500">

        <label for="available_time">Available time</label>
        <input type="text" id="available_time" name="available_time" placeholder="Sun-Thu, 5 PM - 8 PM">

        <label for="room">Room</label>
        <input type="text" id="room" name="room">

        <input type="submit" value="Add doctor" class="btn">
    </form>
</div>

<div class="card">
    <div class="nav-label">All doctors (<?php echo count($doctors); ?>)</div>

    <div class="table-wrap"><table>
        <tr>
            <th>Name</th><th>Department</th><th>Specialization</th>
            <th>Fee</th><th>Room</th><th>Available</th><th></th>
        </tr>
        <?php foreach ($doctors as $doc): ?>
        <tr>
            <td><?php echo $doc["full_name"]; ?><br>
                <span class="muted"><?php echo $doc["email"]; ?></span></td>
            <td><span class="pill"><?php echo $doc["dept_name"]; ?></span></td>
            <td><?php echo $doc["specialization"]; ?></td>
            <td><?php echo (int) $doc["consultation_fee"]; ?> Tk</td>
            <td><?php echo $doc["room"]; ?></td>
            <td><?php echo $doc["available_time"]; ?></td>
            <td>
                <form method="post" action="<?php echo BASE_URL; ?>/index.php?url=admin/doctors">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="doctor_id" value="<?php echo $doc["doctor_id"]; ?>">
                    <button type="submit" class="btn btn-small btn-ghost">Delete</button>
                </form>
            </td>
        </tr>
        <tr>
            <td colspan="7">
                <form method="post" action="<?php echo BASE_URL; ?>/index.php?url=admin/doctors">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="doctor_id" value="<?php echo $doc["doctor_id"]; ?>">

                    <select name="dept_id">
                        <?php foreach ($departments as $d): ?>
                        <option value="<?php echo $d["dept_id"]; ?>"
                            <?php echo $d["dept_id"] == $doc["dept_id"] ? " selected" : ""; ?>>
                            <?php echo $d["dept_name"]; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                    <input type="text" name="specialization"   value="<?php echo $doc["specialization"]; ?>">
                    <input type="text" name="consultation_fee" value="<?php echo (int) $doc["consultation_fee"]; ?>">
                    <input type="text" name="available_time"   value="<?php echo $doc["available_time"]; ?>">
                    <input type="text" name="room"             value="<?php echo $doc["room"]; ?>">

                    <button type="submit" class="btn btn-small">Save</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table></div>
</div>
