<?xml version="1.0" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="../banshee/main.xslt" />

<!--
//
//  Users template
//
//-->
<xsl:template match="users">
<table class="table table-striped table-hover table-condensed">
<thead>
<tr><th class="username">Username</th><th class="name">Name</th><th class="email">E-mail address</th><th class="switch">Switch</th></tr>
</thead>
<tbody>
<xsl:for-each select="user">
<tr>
<td><xsl:value-of select="username" /></td>
<td><xsl:value-of select="fullname" /></td>
<td><xsl:value-of select="email" /></td>
<td><form action="/{/output/page}" method="post"><input type="hidden" name="user_id" value="{@id}" /><input type="submit" value="switch" class="btn btn-xs btn-primary" /></form></td>
</tr>
</xsl:for-each>
</tbody>
</table>

<div class="btn-group">
<a href="/cms" class="btn btn-default">Back</a>
</div>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<img src="/images/icons/switch.png" class="title_icon" />
<h1>User switch</h1>
<xsl:apply-templates select="users" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
