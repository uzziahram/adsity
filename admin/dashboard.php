<?php
require_once __DIR__ . '/dashboard_function.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin Dashboard - Adsity</title>
	<link rel="stylesheet" href="../style.css">
	<link rel="stylesheet" href="admindashboard.css">
	<!-- Google Font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>

<body style="background-color: #f8fafc;">

	<!-- Navigation Header -->
	<header class="navbar">
		<div class="nav-left">
			<a href="../index.php" class="logo">
				<span class="logo-icon">
					<img class="icon" src="../assets/adsity_assets/Adsity_Logo.png" alt="Adsity Logo">
				</span>
			</a>
			<span style="font-weight: 700; color: #0f172a; font-size: 1.05rem;">Admin Control Center</span>
		</div>

		<div class="nav-right">
			<a href="../index.php" class="teach-link">View Website</a>
			<a href="../logout.php" class="btn-login" style="background-color: #475569;">Log Out</a>
		</div>
	</header>

	<main class="admin-container">

		<!-- Admin Header Title -->
		<div class="admin-header">
			<div>
				<h1 class="admin-title">User Monitoring &amp; Management</h1>
				<p class="admin-subtitle">Live overview of registered teachers, instructors, and students across Adsity.</p>
			</div>
		</div>

		<!-- Status Alerts -->
		<?php if ($status === 'success'): ?>
			<div class="alert alert--success">
				<img src="../assets/icons/check-circle.svg" width="20" height="20" alt="Success">
				<span><?= htmlspecialchars($message ?? 'Operation successful!') ?></span>
			</div>
		<?php elseif ($status === 'error'): ?>
			<div class="alert alert--error">
				<img src="../assets/icons/alert-circle.svg" width="20" height="20" alt="Error">
				<span><?= htmlspecialchars($message ?? 'An error occurred.') ?></span>
			</div>
		<?php endif; ?>

		<!-- KPI Metric Cards -->
		<div class="stats-grid">
			<div class="stat-card">
				<div class="stat-icon-box stat-icon-box--blue">
					<img src="../assets/icons/users.svg" width="24" height="24" alt="Users">
				</div>
				<div>
					<div class="stat-number"><?= $totalUsers ?></div>
					<div class="stat-label">Total Users</div>
				</div>
			</div>

			<div class="stat-card">
				<div class="stat-icon-box stat-icon-box--purple">
					<img src="../assets/icons/graduation-cap.svg" width="24" height="24" alt="Instructors">
				</div>
				<div>
					<div class="stat-number"><?= $totalInstructors ?></div>
					<div class="stat-label">Teachers / Instructors</div>
				</div>
			</div>

			<div class="stat-card">
				<div class="stat-icon-box stat-icon-box--green">
					<img src="../assets/icons/user-check.svg" width="24" height="24" alt="Students">
				</div>
				<div>
					<div class="stat-number"><?= $totalStudents ?></div>
					<div class="stat-label">Registered Students</div>
				</div>
			</div>

			<div class="stat-card">
				<div class="stat-icon-box stat-icon-box--amber">
					<img src="../assets/icons/dollar-sign.svg" width="24" height="24" alt="Payouts">
				</div>
				<div>
					<div class="stat-number">$<?= number_format($pendingPayoutAmount, 2) ?></div>
					<div class="stat-label"><?= $pendingPayoutCount ?> Pending Payout<?= $pendingPayoutCount === 1 ? '' : 's' ?></div>
				</div>
			</div>

			<div class="stat-card">
				<div class="stat-icon-box stat-icon-box--green">
					<img src="../assets/icons/award.svg" width="24" height="24" alt="Disbursed">
				</div>
				<div>
					<div class="stat-number">$<?= number_format($totalDisbursed, 2) ?></div>
					<div class="stat-label">Total Disbursed</div>
				</div>
			</div>
		</div>

		<!-- Instructor Payout / Cash-Out Requests Table -->
		<section class="dashboard-section" id="payouts">
			<div class="section-heading">
				<div>
					<h2>
						<img src="../assets/icons/dollar-sign.svg" width="22" height="22" alt="Payouts">
						Instructor Cash-Out &amp; Payout Requests
					</h2>
					<p style="color: #64748b; font-size: 0.88rem; margin: 4px 0 0 0;">
						Review instructor withdrawal requests, verify destination accounts, and disburse ad revenue shares.
					</p>
				</div>
				<span class="badge-count" style="background-color: <?= $pendingPayoutCount > 0 ? '#fef3c7' : '#f1f5f9' ?>; color: <?= $pendingPayoutCount > 0 ? '#b45309' : '#475569' ?>; font-weight: 800;">
					<?= $pendingPayoutCount ?> Pending
				</span>
			</div>

			<div class="table-responsive">
				<?php if (!empty($payoutRequests)): ?>
					<table class="data-table">
						<thead>
							<tr>
								<th>Request ID</th>
								<th>Instructor</th>
								<th>Amount</th>
								<th>Mode of Transfer</th>
								<th>Destination Account</th>
								<th>Date Requested</th>
								<th>Status</th>
								<th style="text-align: right;">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($payoutRequests as $pr): ?>
								<?php $details = json_decode($pr['payout_details'], true) ?: []; ?>
								<tr>
									<td><strong>#<?= $pr['id'] ?></strong></td>
									<td>
										<strong><?= htmlspecialchars($pr['instructor_name']) ?></strong>
										<div style="font-size: 0.8rem; color: #64748b;"><?= htmlspecialchars($pr['instructor_email']) ?></div>
									</td>
									<td>
										<strong style="font-size: 1.05rem; color: #0f172a;">
											$<?= number_format((float)$pr['amount'], 2) ?>
										</strong>
									</td>
									<td>
										<?php if ($pr['payout_method'] === 'gcash'): ?>
											<span class="user-badge-student" style="background-color: #ecfdf5; color: #059669; font-weight: 800;">📱 GCash</span>
										<?php elseif ($pr['payout_method'] === 'paypal'): ?>
											<span class="user-badge-instructor" style="font-weight: 800;">🅿️ PayPal</span>
										<?php else: ?>
											<span class="badge-count" style="font-weight: 800;">🏦 Bank Transfer</span>
										<?php endif; ?>
									</td>
									<td>
										<div style="font-size: 0.85rem; color: #334155;">
											<?php if ($pr['payout_method'] === 'paypal'): ?>
												<span><?= htmlspecialchars($details['email'] ?? '—') ?></span>
											<?php elseif ($pr['payout_method'] === 'gcash'): ?>
												<strong><?= htmlspecialchars($details['account_name'] ?? '') ?></strong>
												<div style="font-size: 0.8rem; color: #64748b;"><?= htmlspecialchars($details['mobile_number'] ?? '') ?></div>
											<?php else: ?>
												<strong><?= htmlspecialchars($details['bank_name'] ?? '') ?></strong>
												<div style="font-size: 0.8rem; color: #64748b;"><?= htmlspecialchars($details['account_name'] ?? '') ?> (<?= htmlspecialchars($details['account_number'] ?? '') ?>)</div>
											<?php endif; ?>
										</div>
									</td>
									<td><?= date('M d, Y h:i A', strtotime($pr['created_at'])) ?></td>
									<td>
										<?php if ($pr['status'] === 'completed'): ?>
											<span class="status-badge-completed">✓ Disbursed</span>
										<?php elseif ($pr['status'] === 'rejected'): ?>
											<span class="status-badge-rejected">✕ Rejected</span>
										<?php else: ?>
											<span class="status-badge-pending">⏳ Pending Review</span>
										<?php endif; ?>
									</td>
									<td style="text-align: right;">
										<button type="button" class="btn-process-payout <?= $pr['status'] !== 'pending' ? 'btn-process-payout--view' : '' ?>" onclick='openAdminPayoutModal(<?= json_encode([
											'id' => (int)$pr['id'],
											'instructor_name' => $pr['instructor_name'],
											'instructor_email' => $pr['instructor_email'],
											'amount' => number_format((float)$pr['amount'], 2),
											'payout_method' => $pr['payout_method'],
											'payout_details' => $details,
											'instructor_notes' => $pr['instructor_notes'] ?? '',
											'admin_notes' => $pr['admin_notes'] ?? '',
											'transaction_reference' => $pr['transaction_reference'] ?? '',
											'status' => $pr['status'],
											'created_at' => date('M d, Y h:i A', strtotime($pr['created_at'])),
											'processed_at' => !empty($pr['processed_at']) ? date('M d, Y h:i A', strtotime($pr['processed_at'])) : '',
											'processed_by_name' => $pr['processed_by_name'] ?? ''
										], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
											<span><?= $pr['status'] === 'pending' ? 'Review &amp; Process' : 'View Details' ?></span>
										</button>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php else: ?>
					<div class="empty-state" style="padding: 40px 20px;">
						<p style="margin: 0; color: #94a3b8;">No instructor withdrawal requests submitted yet.</p>
					</div>
				<?php endif; ?>
			</div>
		</section>

		<!-- Teachers / Instructors Table -->
		<section class="dashboard-section">
			<div class="section-heading">
				<h2>
					<img src="../assets/icons/graduation-cap.svg" width="20" height="20" alt="Instructors">
					Teachers &amp; Instructors
				</h2>
				<span class="badge-count"><?= $totalInstructors ?> Total</span>
			</div>

			<div class="table-responsive">
				<?php if (!empty($instructors)): ?>
					<table class="data-table">
						<thead>
							<tr>
								<th>ID</th>
								<th>Instructor Name</th>
								<th>Email Address</th>
								<th>Role</th>
								<th>Joined Date</th>
								<th style="text-align: right;">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($instructors as $teacher): ?>
								<tr>
									<td>#<?= htmlspecialchars($teacher['id']) ?></td>
									<td>
										<strong><?= htmlspecialchars($teacher['full_name']) ?></strong>
									</td>
									<td><?= htmlspecialchars($teacher['email']) ?></td>
									<td>
										<span class="user-badge-instructor">Instructor</span>
									</td>
									<td><?= htmlspecialchars(date('M d, Y', strtotime($teacher['created_at']))) ?></td>
									<td style="text-align: right;">
										<form method="POST" action="delete_user.php" onsubmit="return confirm('Are you sure you want to delete this instructor?');" style="display: inline;">
											<input type="hidden" name="user_id" value="<?= htmlspecialchars($teacher['id']) ?>">
											<button type="submit" name="delete_user" class="btn-delete">
												<img src="../assets/icons/trash.svg" width="14" height="14" alt="Delete">
												Delete
											</button>
										</form>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php else: ?>
					<div class="empty-state">
						No instructors registered yet.
					</div>
				<?php endif; ?>
			</div>
		</section>

		<!-- Students Table -->
		<section class="dashboard-section">
			<div class="section-heading">
				<h2>
					<img src="../assets/icons/user-check.svg" width="20" height="20" alt="Students">
					Students
				</h2>
				<span class="badge-count"><?= $totalStudents ?> Total</span>
			</div>

			<div class="table-responsive">
				<?php if (!empty($students)): ?>
					<table class="data-table">
						<thead>
							<tr>
								<th>ID</th>
								<th>Student Name</th>
								<th>Email Address</th>
								<th>Role</th>
								<th>Joined Date</th>
								<th style="text-align: right;">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($students as $student): ?>
								<tr>
									<td>#<?= htmlspecialchars($student['id']) ?></td>
									<td>
										<strong><?= htmlspecialchars($student['full_name']) ?></strong>
									</td>
									<td><?= htmlspecialchars($student['email']) ?></td>
									<td>
										<span class="user-badge-student">Student</span>
									</td>
									<td><?= htmlspecialchars(date('M d, Y', strtotime($student['created_at']))) ?></td>
									<td style="text-align: right;">
										<form method="POST" action="delete_user.php" onsubmit="return confirm('Are you sure you want to delete this student?');" style="display: inline;">
											<input type="hidden" name="user_id" value="<?= htmlspecialchars($student['id']) ?>">
											<button type="submit" name="delete_user" class="btn-delete">
												<img src="../assets/icons/trash.svg" width="14" height="14" alt="Delete">
												Delete
											</button>
										</form>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php else: ?>
					<div class="empty-state">
						No students registered yet.
					</div>
				<?php endif; ?>
			</div>
		</section>

	</main>

	<!-- Footer -->
	<footer class="footer">
		<div class="footer-bottom">
			<div class="footer-bottom-inner">
				<img src="../assets/adsity_assets/Adsity_Logo.png" alt="Adsity Logo" class="footer-bottom-logo-img">
				<p class="footer-copyright">&copy; 2026 Adsity Admin Portal. All rights reserved.</p>
			</div>
		</div>
	</footer>

	<!-- ==========================================================
	     ADMIN PAYOUT REVIEW & DISBURSEMENT MODAL
	     ========================================================== -->
	<div id="adminPayoutModal" class="admin-modal-backdrop" style="display: none;" onclick="handleAdminModalBackdrop(event)">
		<div class="admin-modal-dialog">
			<div class="admin-modal-header">
				<div>
					<h3 class="admin-modal-title">
						<img src="../assets/icons/dollar-sign.svg" width="20" height="20" alt="Review">
						Review Cash-Out Request <span id="modalPayoutIdDisplay">#0</span>
					</h3>
					<p class="admin-modal-subtitle">Verify the instructor's payout destination and disburse earnings.</p>
				</div>
				<button type="button" class="admin-modal-close" onclick="closeAdminPayoutModal()" aria-label="Close modal">&times;</button>
			</div>

			<div class="admin-modal-body">
				<!-- Meta Overview Card -->
				<div class="admin-meta-card">
					<div class="admin-meta-item">
						<span class="admin-meta-label">Instructor</span>
						<strong id="modalInstructorName" class="admin-meta-value">-</strong>
						<span id="modalInstructorEmail" style="font-size: 0.78rem; color: #64748b;">-</span>
					</div>
					<div class="admin-meta-item">
						<span class="admin-meta-label">Requested Amount</span>
						<strong id="modalAmountDisplay" style="font-size: 1.3rem; color: #16a34a;">$0.00</strong>
					</div>
					<div class="admin-meta-item">
						<span class="admin-meta-label">Status</span>
						<div id="modalStatusBadge" style="margin-top: 4px;">-</div>
					</div>
					<div class="admin-meta-item">
						<span class="admin-meta-label">Date Requested</span>
						<span id="modalCreatedAt" style="font-size: 0.85rem; color: #0f172a;">-</span>
					</div>
				</div>

				<!-- Destination Details Card -->
				<div>
					<label class="admin-meta-label" style="display: block; margin-bottom: 6px;">Transfer Destination &amp; Account Details</label>
					<div class="admin-details-box" id="modalDestinationBox">
						<!-- Populated via JS -->
					</div>
				</div>

				<!-- Instructor Notes Box (if any) -->
				<div id="modalInstructorNotesWrapper" style="display: none;">
					<label class="admin-meta-label" style="display: block; margin-bottom: 6px;">Instructor Notes</label>
					<div style="background-color: #f1f5f9; border-left: 3px solid #0284c7; padding: 10px 14px; border-radius: 6px; font-size: 0.88rem; color: #334155;" id="modalInstructorNotes">-</div>
				</div>

				<!-- Processing History (if already completed or rejected) -->
				<div id="modalProcessedHistoryWrapper" style="display: none; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px;">
					<div style="font-size: 0.78rem; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 6px;">Processing Log</div>
					<div style="font-size: 0.88rem; color: #334155; margin-bottom: 4px;">
						<strong>Reference ID:</strong> <span id="modalHistoryRef">-</span>
					</div>
					<div style="font-size: 0.88rem; color: #334155; margin-bottom: 4px;">
						<strong>Admin Notes:</strong> <span id="modalHistoryNotes">-</span>
					</div>
					<div style="font-size: 0.78rem; color: #94a3b8;" id="modalHistoryMeta">Processed on ...</div>
				</div>

				<!-- Active Processing Form (Only shown when pending) -->
				<form id="adminPayoutActionForm" method="POST" action="process_payout_function.php">
					<input type="hidden" name="payout_id" id="modalPayoutIdInput" value="">
					<input type="hidden" name="action" id="modalActionInput" value="">

					<div id="modalActionFields">
						<div style="margin-bottom: 16px;">
							<label for="modalTransactionRef" class="admin-meta-label" style="display: block; margin-bottom: 6px;">
								Disbursement Reference ID / Receipt Code <span style="font-weight: 500; font-size: 0.78rem; color: #64748b;">(Optional for Approval)</span>
							</label>
							<input type="text" id="modalTransactionRef" name="transaction_reference" placeholder="e.g. PayPal TXN ID, GCash Reference No., or Bank Trace #" class="form-input" style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.92rem; box-sizing: border-box;">
						</div>

						<div style="margin-bottom: 20px;">
							<label for="modalAdminNotes" class="admin-meta-label" style="display: block; margin-bottom: 6px;">
								Admin Notes &amp; Comments <span style="font-weight: 500; font-size: 0.78rem; color: #dc2626;">(Required if rejecting)</span>
							</label>
							<textarea id="modalAdminNotes" name="admin_notes" rows="3" placeholder="Provide reason if rejecting, or confirmation notes for the instructor..." class="review-textarea" style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.92rem; box-sizing: border-box; font-family: inherit; resize: vertical;"></textarea>
						</div>

						<div class="admin-modal-footer">
							<button type="button" class="btn-delete" style="background-color: #ffffff; border-color: #cbd5e1; color: #475569;" onclick="closeAdminPayoutModal()">Close</button>
							<button type="button" class="btn-delete" onclick="submitAdminPayoutAction('reject')">
								<img src="../assets/icons/alert-circle.svg" width="14" height="14" alt="Reject">
								<span>Reject &amp; Refund Balance</span>
							</button>
							<button type="button" class="btn-process-payout" style="background-color: #16a34a;" onclick="submitAdminPayoutAction('approve')">
								<img src="../assets/icons/check-circle.svg" width="15" height="15" alt="Approve" style="filter: brightness(0) invert(1);">
								<span>Approve &amp; Mark Paid</span>
							</button>
						</div>
					</div>

					<div id="modalCloseOnlyFooter" class="admin-modal-footer" style="display: none;">
						<button type="button" class="btn-process-payout btn-process-payout--view" onclick="closeAdminPayoutModal()">Close</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script>
		function openAdminPayoutModal(data) {
			document.getElementById('modalPayoutIdDisplay').textContent = '#' + data.id;
			document.getElementById('modalPayoutIdInput').value = data.id;
			document.getElementById('modalInstructorName').textContent = data.instructor_name;
			document.getElementById('modalInstructorEmail').textContent = data.instructor_email;
			document.getElementById('modalAmountDisplay').textContent = '$' + data.amount;
			document.getElementById('modalCreatedAt').textContent = data.created_at;

			// Status pill
			var statusHtml = '';
			if (data.status === 'completed') {
				statusHtml = '<span class="status-badge-completed">✓ Disbursed</span>';
			} else if (data.status === 'rejected') {
				statusHtml = '<span class="status-badge-rejected">✕ Rejected</span>';
			} else {
				statusHtml = '<span class="status-badge-pending">⏳ Pending Review</span>';
			}
			document.getElementById('modalStatusBadge').innerHTML = statusHtml;

			// Destination Details
			var destHtml = '';
			var details = data.payout_details || {};
			if (data.payout_method === 'paypal') {
				destHtml = '<div style="font-weight: 800; color: #1d4ed8; margin-bottom: 4px;">🅿️ PayPal Transfer</div>' +
				           '<div><strong>Account Email:</strong> ' + escapeHtml(details.email || '—') + '</div>';
			} else if (data.payout_method === 'gcash') {
				destHtml = '<div style="font-weight: 800; color: #059669; margin-bottom: 4px;">📱 GCash Transfer</div>' +
				           '<div><strong>Account Name:</strong> ' + escapeHtml(details.account_name || '—') + '</div>' +
				           '<div><strong>Mobile Number:</strong> ' + escapeHtml(details.mobile_number || '—') + '</div>';
			} else {
				destHtml = '<div style="font-weight: 800; color: #334155; margin-bottom: 4px;">🏦 Bank Transfer</div>' +
				           '<div><strong>Bank:</strong> ' + escapeHtml(details.bank_name || '—') + '</div>' +
				           '<div><strong>Account Name:</strong> ' + escapeHtml(details.account_name || '—') + '</div>' +
				           '<div><strong>Account Number:</strong> ' + escapeHtml(details.account_number || '—') + '</div>';
			}
			document.getElementById('modalDestinationBox').innerHTML = destHtml;

			// Instructor notes
			var notesWrapper = document.getElementById('modalInstructorNotesWrapper');
			if (data.instructor_notes && data.instructor_notes.trim().length > 0) {
				document.getElementById('modalInstructorNotes').textContent = data.instructor_notes;
				notesWrapper.style.display = 'block';
			} else {
				notesWrapper.style.display = 'none';
			}

			// Active form vs history
			var actionFields = document.getElementById('modalActionFields');
			var historyWrapper = document.getElementById('modalProcessedHistoryWrapper');
			var closeOnlyFooter = document.getElementById('modalCloseOnlyFooter');

			if (data.status === 'pending') {
				actionFields.style.display = 'block';
				historyWrapper.style.display = 'none';
				closeOnlyFooter.style.display = 'none';
				document.getElementById('modalTransactionRef').value = '';
				document.getElementById('modalAdminNotes').value = '';
			} else {
				actionFields.style.display = 'none';
				historyWrapper.style.display = 'block';
				closeOnlyFooter.style.display = 'flex';
				document.getElementById('modalHistoryRef').textContent = data.transaction_reference || 'None specified';
				document.getElementById('modalHistoryNotes').textContent = data.admin_notes || 'No comments';
				document.getElementById('modalHistoryMeta').textContent = 'Processed on ' + (data.processed_at || '—') + (data.processed_by_name ? ' by ' + data.processed_by_name : '');
			}

			var modal = document.getElementById('adminPayoutModal');
			modal.style.display = 'flex';
			document.body.style.overflow = 'hidden';
		}

		function closeAdminPayoutModal() {
			var modal = document.getElementById('adminPayoutModal');
			if (modal) {
				modal.style.display = 'none';
				document.body.style.overflow = '';
			}
		}

		function handleAdminModalBackdrop(event) {
			if (event.target && event.target.id === 'adminPayoutModal') {
				closeAdminPayoutModal();
			}
		}

		function submitAdminPayoutAction(action) {
			var notes = document.getElementById('modalAdminNotes').value.trim();
			if (action === 'reject') {
				if (!notes) {
					alert('Please enter an administrative reason explaining why this withdrawal request is being rejected.');
					document.getElementById('modalAdminNotes').focus();
					return;
				}
				if (!confirm('Are you sure you want to reject this payout request? The amount will be refunded back to the instructor\'s available balance.')) {
					return;
				}
			} else if (action === 'approve') {
				if (!confirm('Confirm disbursement: Have you transferred the funds or are you ready to approve this cash-out request?')) {
					return;
				}
			}

			document.getElementById('modalActionInput').value = action;
			document.getElementById('adminPayoutActionForm').submit();
		}

		function escapeHtml(str) {
			var div = document.createElement('div');
			div.appendChild(document.createTextNode(str));
			return div.innerHTML;
		}
	</script>
	<script src="../assets/js/logout_modal.js"></script>
</body>
</html>
