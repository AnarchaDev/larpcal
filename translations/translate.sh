#!/bin/bash
find ../ -path ../vendor -prune -o -name '*.php' -print > POTFILES
xgettext --from-code=UTF-8 --add-comments --files-from=POTFILES -o MASTER.pot

#msgfmt da_DK.UTF-8/LC_MESSAGES/larpcal-backend.po -o da_DK.UTF-8/LC_MESSAGES/larpcal-backend.mo
#msgfmt en_GB.UTF-8/LC_MESSAGES/larpcal-backend.po -o en_GB.UTF-8/LC_MESSAGES/larpcal-backend.mo
#msgfmt en_US.UTF-8/LC_MESSAGES/larpcal-backend.po -o en_US.UTF-8/LC_MESSAGES/larpcal-backend.mo
#msgfmt fi_FI.UTF-8/LC_MESSAGES/larpcal-backend.po -o fi_FI.UTF-8/LC_MESSAGES/larpcal-backend.mo
#msgfmt nb_NO.UTF-8/LC_MESSAGES/larpcal-backend.po -o nb_NO.UTF-8/LC_MESSAGES/larpcal-backend.mo
#msgfmt sv_SE.UTF-8/LC_MESSAGES/larpcal-backend.po -o sv_SE.UTF-8/LC_MESSAGES/larpcal-backend.mo

msgmerge --update da_DK.UTF-8/LC_MESSAGES/larpcal-backend.po MASTER.POT
msgmerge --update en_GB.UTF-8/LC_MESSAGES/larpcal-backend.po MASTER.POT
msgmerge --update en_US.UTF-8/LC_MESSAGES/larpcal-backend.po MASTER.POT
msgmerge --update fi_FI.UTF-8/LC_MESSAGES/larpcal-backend.po MASTER.POT
msgmerge --update nb_NO.UTF-8/LC_MESSAGES/larpcal-backend.po MASTER.POT
msgmerge --update sv_SE.UTF-8/LC_MESSAGES/larpcal-backend.po MASTER.POT

find ./ -name '*.po~' -print | xargs rm -rf
