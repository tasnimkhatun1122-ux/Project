<h1>Manage departments</h1>
<p class="muted">A department can only be deleted once it has no doctors.</p>

<?php if ($error != ""): ?><div class="alert-error"><?php echo $error; ?></div><?php endif; ?>
<?php if ($success != ""): ?><div class="alert-ok"><?php echo $success; ?></div><?php endif; ?>

<div class="card">
    <div class="nav-label">Add a department</div>

    <form method="post" action="<?php echo BASE_URL; ?>/index.php?url=admin/departments">
        <input type="hidden" name="action" value="add">

        <label for="dept_name">Department name</label>
        <input type="text" id="dept_name" name="dept_name" placeholder="Neurology">

        <input type="submit" value="Add department" class="btn">
    </form>
</div>

<div class="card">
    <div class="nav-label">All departments (<?php echo count($departments); ?>)</div>

    <div class="table-wrap"><table>
        <tr><th>Department</th><th>Doctors</th><th>Rename</th><th></th></tr>
        <?php foreach ($departments as $d): ?>
        <tr>
            <td><?php echo $d["dept_name"]; ?></td>
            <td><?php echo $d["doctors"]; ?></td>
            <td>
                <form method="post" action="<?php echo BASE_URL; ?>/index.php?url=admin/departments">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="dept_id" value="<?php echo $d["dept_id"]; ?>">
                    <input type="text" name="dept_name" value="<?php echo $d["dept_name"]; ?>">
                    <button type="submit" class="btn btn-small">Save</button>
                </form>
            </td>
            <td>
                <?php if ($d["doctors"] == 0): ?>
                <form method="post" action="<?php echo BASE_URL; ?>/index.php?url=admin/departments">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="dept_id" value="<?php echo $d["dept_id"]; ?>">
                    <button type="submit" class="btn btn-small btn-ghost">Delete</button>
                </form>
                <?php else: ?>
                    <span class="muted">In use</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table></div>
</div>
