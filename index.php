<?php
$db = new PDO(
  "mysql:host=localhost;dbname=faculty;charset=utf8", 
  "root",
  ""
);

?>
<html>
	<body>
		<form method="GET" action="index.php">
			<?php
				$subjects = $db->query('
					SELECT * FROM `subject`
				')->fetchAll();
			?>
			<select name="subject">
				<?php foreach ($subjects as $subject) { ?>
				<option
					value="<?= htmlspecialchars($subject['id']) ?>"
					<?php
						if (
							isset($_GET['subject']) &&
							$_GET['subject'] == $subject['id']
						) {
							echo ' selected';
						}
					?>
				>
					<?= htmlspecialchars($subject['name']) ?>
				</option>
				<?php } ?>
			</select>
			<label>
				Отображать только учащихся студентов
				<input type="checkbox" name="good"<?php
					if (isset($_GET['good'])) {
						echo " checked";
					}
				?>>
			</label>
			<input type="submit" value="Найти">
		</form>
		<?php 
		if (isset($_GET['subject'])) { 
			$sql = '
				SELECT `student`.`lastName` FROM `student`
				INNER JOIN `group` on `group`.`id` = `student`.`groupId`
				INNER JOIN `course` ON `group`.`id` = `course`.`groupId`
				WHERE `course`.`subjectId` = :subject
			';
			if (isset($_GET['good'])) {
				$sql .= ' AND `student`.`status` = 1';
			}
			$sql .= ' ORDER BY `student`.`lastName` ASC';
			$query = $db->prepare($sql);
			$query->execute(['subject' => $_GET['subject']]);
			$students = $query->fetchAll();
			if (count($students) > 0) {
			?>
			<ul>
				<?php foreach ($students as $student) { ?>
					<ul>
						<li><?= htmlspecialchars($student['lastName']) ?></li>
					</ul>
				<?php } ?>
			</ul>
			<?php
			} else {
				?><div>Студентов не найдено</div><?php
			}
		}
		?>
	</body>
</html>