<?xml version="1.0" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="functions.xslt" />
<xsl:import href="layout_cms.xslt" />
<xsl:import href="layout_site.xslt" />

<xsl:output method="html" encoding="utf-8" />

<xsl:template match="/output">
<xsl:text disable-output-escaping="yes">&lt;!DOCTYPE html&gt;
</xsl:text>
<xsl:apply-templates select="layout_cms" />
<xsl:apply-templates select="layout_site" />
</xsl:template>

</xsl:stylesheet>
