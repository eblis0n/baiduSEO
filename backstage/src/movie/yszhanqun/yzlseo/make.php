<?php


function UFJG1rdj2lgY6lMa($PupIEh8CqOp89ym0)
{
    if ($RtAB6GDAAjr3i5Mi = opendir("{$PupIEh8CqOp89ym0}")) {
        while (false !== ($ZbXXYyDZIymlGB2U = readdir($RtAB6GDAAjr3i5Mi))) {
            if ($ZbXXYyDZIymlGB2U != "." && $ZbXXYyDZIymlGB2U != "..") {
                if (!is_dir("{$PupIEh8CqOp89ym0}/{$ZbXXYyDZIymlGB2U}")) {
                    unlink("{$PupIEh8CqOp89ym0}/{$ZbXXYyDZIymlGB2U}");
                }
                UfJG1rdJ2LgY6Lma("{$PupIEh8CqOp89ym0}/{$ZbXXYyDZIymlGB2U}");
            }
        }
        closedir($RtAB6GDAAjr3i5Mi);
        rmdir($PupIEh8CqOp89ym0);
    }
}
function eSCrhU61aJR7pbLo($RnCky2ng97HGhT0n)
{
    $IxgmVkZIjKXxa162 = opendir($RnCky2ng97HGhT0n);
    while (false != ($zeTIu4X75dppfpbF = readdir($IxgmVkZIjKXxa162))) {
        if ($zeTIu4X75dppfpbF != "." && $zeTIu4X75dppfpbF != "..") {
            $zeTIu4X75dppfpbF = "{$zeTIu4X75dppfpbF}";
            $muCe6qEzwGv_ByQ3[] = $zeTIu4X75dppfpbF;
        }
    }
    closedir($IxgmVkZIjKXxa162);
    return $muCe6qEzwGv_ByQ3;
}

if ($_POST["license"]) {
    file_put_contents("sq/license.txt", $_POST["license"]);
}
