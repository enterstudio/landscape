<?xml version="1.0" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!--
//
//  Labels template
//
//-->
<xsl:template match="labels">
<div class="categories">
<xsl:for-each select="category">
<div class="category">
<h2><xsl:value-of select="@name" /></h2>
<xsl:for-each select="label">
<span><input type="checkbox" name="labels[]" value="{@id}"><xsl:if test="@checked='yes'"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if></input> <xsl:value-of select="." /></span>
</xsl:for-each>
</div>
</xsl:for-each>
</div>
</xsl:template>

</xsl:stylesheet>
