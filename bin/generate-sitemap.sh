#! /bin/sh -

# Application PATH.
APP_PATH="$( cd -P "$( dirname "$0" )/../" && pwd )"

# Application name.
APP_NAME="$( basename "$APP_PATH" )"

# PATH to document root.
DOCUMENT_ROOT=${APP_PATH}/www

# PATH to Sitemap Generator installation directory.
SITEMAP=${APP_PATH}/lib/3rdparty/sitemap_gen

# PATH to store the list of list generated URLs.
URLLIST=/tmp/${APP_NAME}-urllist.txt

echo "Generating '${URLLIST}'."
${APP_PATH}/bin/generate-urllist.php > ${URLLIST}

echo "Removing previous sitemaps."
rm -f ${DOCUMENT_ROOT}/sitemap*.xml*

echo "Running Google sitemap generator."
python ${SITEMAP}/sitemap_gen.py --config=${APP_PATH}/etc/sitemap_gen.xml

echo "Removing '${URLLIST}'."
rm -f ${URLLIST}
