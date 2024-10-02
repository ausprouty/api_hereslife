<?php
require_once __DIR__ .'/router.php';


$path = PATH;
//error_log ($path . 'spirit/titles');
get($path, '/App/Views/indexLocal.php');
get($path . 'admin/exists', 'App/API/People/AdminExists.php');
get($path . 'cron/tips/first', 'App/API/Emails/QueFirstTips.php');
get($path . 'cron/tips/next', 'App/API/Emails/QueNextTips.php');
get($path . 'cron/emails/send', 'App/API/Emails/SendEmailFromQue.php');


get($path . 'email/$id', 'App/API/Emails/GetEmail.php');
get($path . 'email/ad/recent/$number', 'App/API/Emails/AddRecentTitles.php');
get($path . 'email/blog/recent/$number', 'App/API/Emails/BlogRecentTitles.php');
get($path . 'email/series/titles/$series', 'App/API/Emails/SeriesEmailTitles.php');
get($path . 'email/series/$series/$sequence', 'App/API/Emails/SeriesEmailText.php');
get($path . 'email/tracking/$champion_id/$email_id', 'App/API/Emails/EmailTrackOpen.php');
get($path . 'email/view/$id', 'App/API/Emails/GetEmailView.php');
get($path . 'import/champions', 'App/API/Import/ImportChampions.php');
get($path . 'import/email/que', 'App/API/Import/ImportEmailQue.php');
get($path . 'import/email/nodes', 'App/API/Import/SetupEmailNodeMigration.php');
get($path . 'import/email/node/content', 'App/API/Import/ImportEmailNodes.php');
get($path . 'import/list/members', 'App/API/Import/ImportEmailSeriesMembers.php');
get($path . 'import/materials', 'App/API/Import/ImportMaterials.php');
get($path . 'import/blog', 'App/API/Import/ImportBlog.php');
get($path . 'import/downloads', 'App/API/Import/ImportDownloads.php');
get($path . 'spirit/text/$language', 'App/API/Materials/getSpiritText.php');
get($path . 'spirit/titles', 'App/API/Materials/getSpiritTitles.php');
get($path . 'test', 'App/API/Materials/getTractsToView.php');
get($path . 'tracts/view', 'App/API/Materials/getTractsToView.php');

get($path . 'tracts/options/lang1/$tract_type', 'App/API/Materials/getTractOptionsLanguage1.php');
get($path . 'tracts/options/lang2/$tract_type/$lang1', 'App/API/Materials/getTractOptionsLanguage2.php');
get($path . 'tracts/options/audience/$tract_type/$lang1/$lang2', 'App/API/Materials/getTractOptionsAudience.php');
get($path . 'tracts/options/pagesize/$tract_type/$lang1/$lang2/$audience', 'App/API/Materials/getTractOptionsPageSize.php');
get($path . 'tracts/options/contacts/$tract_type/$lang1/$lang2/$audience/$pagesize', 'App/API/Materials/getTractOptionsContacts.php');

post($path . 'admin/create', 'App/API/People/AdminCreate.php', $postData);
post($path . 'admin/login', 'App/API/People/AdminLogin.php', $postData);
post($path . 'admin/users/unsubscribe', 'App/API/People/AdminUsersUnsubscribe.php', $postData);
post($path . 'email/images', 'App/API/Emails/UploadImages.php');
post($path . 'email/images/upload', 'App/API/Emails/UploadImages.php');
post($path . 'email/images/upload/tinymce', 'App/API/Emails/UploadImagesTinyMce.php');
post($path . 'email/images/upload/tinymce2', 'App/API/Emails/UploadImagesTinyMce2.php');
post($path . 'email/que/group', 'App/API/Emails/QueEmailForGroup.php',$postData);
post($path . 'email/send/direct', 'App/API/Emails/SendEmail.php',$postData);
post($path . 'email/series/text/update', 'App/API/Emails/SeriesEmailTextUpdate.php',$postData);
post($path . 'materials/download', 'App/API/Materials/DownloadMaterialsUpdateUser.php', $postData);
post($path . 'user/unsubscribe', 'App/API/People/UserUnsubscribe.php', $postData);
post($path . 'user/update', 'App/API/People/UserUpdate.php', $postData);
if (ENVIRONMENT == 'local'){
    get($path . 'test/spirit/titles', 'App/Tests/canGetSpiritTitlesByLanguage.php');
    get($path . 'test/token/$cid', 'App/Tests/returnTokenForUser.php');
    get($path . 'test/tracts/view', 'App/Tests/canGetTractsToView.php');
    get($path . 'test/tracts/monolingual', 'App/Tests/canGetTractsMonolingual.php');
    get($path . 'test/tracts/bilingual/english', 'App/Tests/canGetTractsBilingualEnglish.php');
    get($path . '/rcd/test', 'App/Tests/canAccessFromWordPress.php');
}

any($path . '*', function() {
    header("HTTP/1.1 404 Not Found");
    echo "404 Not Found: The requested resource could not be found.";
    exit;
});
