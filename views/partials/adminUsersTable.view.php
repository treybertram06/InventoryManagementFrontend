<?php
/**
 * @var Models\User $currentUser
 * @var Models\User[] $users
 * @var int $adminCount
 * @var array|null $userErrors
 * @var array|null $createUserErrors
 * @var array|null $createUserValues
 */
$userErrors ??= [];
$createUserErrors ??= [];
$createUserValues ??= [];

$inputClasses = 'w-full rounded-md border border-text-muted/25 bg-surface px-3 py-2 text-sm text-text placeholder:text-text-muted focus:border-primary focus:outline-none dark:bg-surface-dark dark:text-white';
$labelClasses = 'mb-1.5 block text-sm font-medium text-text dark:text-white';
?>
<div class="overflow-hidden rounded-md border border-text-muted/20 bg-surface shadow-sm dark:bg-surface-dark">
    <div class="border-b border-text-muted/20 p-4">
        <h2 class="text-lg font-semibold text-text dark:text-white">Users</h2>
    </div>

    <?php if (!empty($userErrors)): ?>
        <div class="m-4 rounded-md border border-red-500/30 bg-red-500/10 p-3">
            <ul class="list-inside list-disc text-sm text-red-600 dark:text-red-400">
                <?php foreach ($userErrors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="overflow-auto">
        <table class="min-w-full divide-y divide-text-muted/20">
            <thead class="bg-surface-muted dark:bg-surface-muted-dark">
            <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-muted uppercase dark:text-white/70">Username</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-muted uppercase dark:text-white/70">Email</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-muted uppercase dark:text-white/70">Role</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-muted uppercase dark:text-white/70">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-text-muted/10">
            <?php foreach ($users as $u): ?>
                <?php
                    $isSelf = $u->ID === $currentUser->ID;
                    $isOnlyAdmin = $u->role === Models\UserRole::Admin && $adminCount <= 1;
                    $lockRole = $isSelf || $isOnlyAdmin;
                ?>
                <tr class="hover:bg-surface-muted/50 dark:hover:bg-surface-muted-dark/50">
                    <td class="px-4 py-3 text-sm font-medium whitespace-nowrap text-text dark:text-white"><?= htmlspecialchars($u->username) ?></td>
                    <td class="px-4 py-3 text-sm whitespace-nowrap text-text-muted dark:text-white/70"><?= htmlspecialchars($u->email) ?></td>
                    <td class="px-4 py-3 text-sm whitespace-nowrap">
                        <form method="POST" action="/admin-user-role" onsubmit="return confirm('Change this user\'s role?');">
                            <input type="hidden" name="user_id" value="<?= $u->ID ?>">
                            <select name="role" class="<?= $inputClasses ?>" <?= $lockRole ? 'disabled' : '' ?> onchange="this.form.submit()">
                                <?php foreach (Models\UserRole::cases() as $case): ?>
                                    <option value="<?= $case->value ?>" <?= $u->role === $case ? 'selected' : '' ?>><?= ucfirst($case->value) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-sm whitespace-nowrap">
                        <?php if ($isSelf): ?>
                            <span class="text-text-muted dark:text-white/50">You</span>
                        <?php elseif ($isOnlyAdmin): ?>
                            <span class="text-text-muted dark:text-white/50">Last admin</span>
                        <?php else: ?>
                            <form method="POST" action="/admin-user-delete" class="inline" onsubmit="return confirm('Delete this user? This cannot be undone.');">
                                <input type="hidden" name="user_id" value="<?= $u->ID ?>">
                                <button type="submit" class="font-medium text-red-600 hover:underline dark:text-red-400">Delete</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="border-t border-text-muted/20 p-4">
        <h3 class="text-sm font-semibold text-text dark:text-white">Create User</h3>

        <?php if (!empty($createUserErrors)): ?>
            <div class="mt-3 rounded-md border border-red-500/30 bg-red-500/10 p-3">
                <ul class="list-inside list-disc text-sm text-red-600 dark:text-red-400">
                    <?php foreach ($createUserErrors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="/admin-user-create" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label for="create-username" class="<?= $labelClasses ?>">Username</label>
                <input id="create-username" type="text" name="username" value="<?= htmlspecialchars($createUserValues['username'] ?? '') ?>" class="<?= $inputClasses ?>">
            </div>
            <div>
                <label for="create-email" class="<?= $labelClasses ?>">Email</label>
                <input id="create-email" type="email" name="email" value="<?= htmlspecialchars($createUserValues['email'] ?? '') ?>" class="<?= $inputClasses ?>">
            </div>
            <div>
                <label for="create-password" class="<?= $labelClasses ?>">Password</label>
                <input id="create-password" type="password" name="password" class="<?= $inputClasses ?>">
            </div>
            <div>
                <label for="create-role" class="<?= $labelClasses ?>">Role</label>
                <select id="create-role" name="role" class="<?= $inputClasses ?>">
                    <?php foreach (Models\UserRole::cases() as $case): ?>
                        <option value="<?= $case->value ?>" <?= ($createUserValues['role'] ?? 'technician') === $case->value ? 'selected' : '' ?>><?= ucfirst($case->value) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="sm:col-span-2 lg:col-span-4">
                <button type="submit" class="rounded-md bg-primary px-5 py-2.5 text-sm font-medium text-white transition hover:bg-primary-hover">
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>
