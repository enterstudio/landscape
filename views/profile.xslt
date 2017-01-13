<?xml version="1.0" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="banshee/main.xslt" />

<!--
//
//  Edit template
//
//-->
<xsl:template match="edit">
<xsl:call-template name="show_messages" />
<form action="/{/output/page}" method="post">
<label for="fullname">Name:</label>
<input type="text" id="fullname" name="fullname" value="{fullname}" class="form-control" />
<label for="email">E-mail address:</label>
<input type="text" id="email" name="email" value="{email}" class="form-control" />
<label for="current">Current password:</label>
<input type="password" id="current" name="current" class="form-control" />
<label for="password">New password:</label> <span class="blank" style="font-size:10px">(will not be changed when left blank)</span>
<input type="password" id="password" name="password" class="form-control" />
<label for="repeat">Repeat password:</label>
<input type="password" id="repeat" name="repeat" class="form-control" />

<div class="btn-group">
<input type="submit" name="submit_button" value="Update profile" class="btn btn-default" />
<xsl:if test="cancel">
<a href="/{cancel/@url}" class="btn btn-default"><xsl:value-of select="cancel" /></a>
</xsl:if>
</div>
</form>

<h2>Recent account activity</h2>
<table class="table table-striped table-xs">
<thead>
<tr>
<th>IP address</th>
<th>Timestamp</th>
<th>Activity</th>
</tr>
</thead>
<tbody>
<xsl:for-each select="actionlog/log">
<tr>
<td><xsl:value-of select="ip" /></td>
<td><xsl:value-of select="timestamp" /></td>
<td><xsl:value-of select="message" /></td>
</tr>
</xsl:for-each>
</tbody>
</table>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>User profile</h1>
<xsl:apply-templates select="edit" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
