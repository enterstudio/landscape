<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="../banshee/main.xslt" />

<!--
//
//  Overview template
//
//-->
<xsl:template match="overview">
<xsl:if test="count(categories/category)>0">
<form action="/{/output/page}" method="post" class="category">
<select id="category" name="category" class="form-control" onChange="javascript:submit()">
<xsl:for-each select="categories/category">
<option value="{@id}"><xsl:if test="@id=../@current"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if><xsl:value-of select="name" /></option>
</xsl:for-each>
</select>
<input type="hidden" name="submit_button" value="category" />
</form>
</xsl:if>

<table class="table table-condensed table-striped table-hover">
<thead>
<tr><th>Name</th></tr>
</thead>
<tbody>
<xsl:for-each select="labels/label">
<tr class="click" onClick="javascript:document.location='/{/output/page}/{@id}'">
<td><xsl:value-of select="name" /></td>
</tr>
</xsl:for-each>
</tbody>
</table>

<div class="btn-group left">
<a href="/{/output/page}/new" class="btn btn-default">New label</a>
<a href="/{/output/page}/category" class="btn btn-default">Edit categories</a>
<a href="/cms" class="btn btn-default">Back</a>
</div>
</xsl:template>

<!--
//
//  Edit template
//
//-->
<xsl:template match="edit">
<xsl:call-template name="show_messages" />
<form action="/{/output/page}" method="post">
<xsl:if test="label/@id">
<input type="hidden" name="id" value="{label/@id}" />
</xsl:if>

<label for="name">Name:</label>
<input type="text" id="name" name="name" value="{label/name}" class="form-control" />

<div class="btn-group">
<input type="submit" name="submit_button" value="Save label" class="btn btn-default" />
<a href="/{/output/page}" class="btn btn-default">Cancel</a>
<xsl:if test="label/@id">
<input type="submit" name="submit_button" value="Delete label" class="btn btn-default" onClick="javascript:return confirm('DELETE: Are you sure?')" />
</xsl:if>
</div>
</form>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<img src="/images/icons/labels.png" class="title_icon" />
<h1>Label administration</h1>
<xsl:apply-templates select="overview" />
<xsl:apply-templates select="edit" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
