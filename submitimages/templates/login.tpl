{if $messages.loginFailed}
The login was not successful. Please try again. If problem persists, please contact an administrator.
{else}
{$user.username|escape:'htmlall'}, you have been successfully logged in.{if $user.moderator} You are also recognized as an image moderator.{/if}
{/if}
