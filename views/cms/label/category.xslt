<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="../../banshee/main.xslt" />

<!--
//
//  Overview template
//
//-->
<xsl:template match="overview">
<table class="table table-condensed table-striped table-hover">
<thead>
<tr><th>Name</th></tr>
</thead>
<tbody>
<xsl:for-each select="categories/category">
<tr class="click" onClick="javascript:document.location='/{/output/page}/{@id}'">
<td><xsl:value-of select="name" /></td>
</tr>
</xsl:for-each>
</tbody>
</table>

<div class="btn-group left">
<a href="/{/output/page}/new" class="btn btn-default">New category</a>
<a href="/cms/label" class="btn btn-default">Back</a>
</div>

<div id="help">
<p>Add categories for your labels, like 'Information' for the labels 'financial information', 'personel information' or 'customer information' or 'Process' for the labels 'sales', 'production', 'sales' or 'marketing'.</p>
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
<xsl:if test="category/@id">
<input type="hidden" name="id" value="{category/@id}" />
</xsl:if>

<label for="name">Name:</label>
<input type="text" id="name" name="name" value="{category/name}" class="form-control" />

<div class="btn-group">
<input type="submit" name="submit_button" value="Save category" class="btn btn-default" />
<a href="/{/output/page}" class="btn btn-default">Cancel</a>
<xsl:if test="category/@id">
<input type="submit" name="submit_button" value="Delete category" class="btn btn-default" onClick="javascript:return confirm('DELETE: Are you sure?')" />
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
<h1>Label category administration</h1>
<xsl:apply-templates select="overview" />
<xsl:apply-templates select="edit" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
