#!/bin/bash
cd /Users/mhill/projects/nix
exec ctags -f ~/.vim/mytags/tsk_symfony \
-h \".php\" -R \
--exclude=\"\.git\" \
--language=php \
--totals=yes \
--tag-relative=yes \
--PHP-kinds=+cf \
--regex-PHP='/abstract class ([^ ]*)/\1/c/' \
--regex-PHP='/interface ([^ ]*)/\1/c/' \
--regex-PHP='/(public |static |abstract |protected |private )+function ([^ (]*)/\2/f/'
