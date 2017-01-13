<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="../banshee/main.xslt" />
<xsl:import href="../banshee/pagination.xslt" />

<!--
//
//  Overview template
//
//-->
<xsl:template match="overview">
<form action="/{/output/page}" method="post" class="search">
<input type="text" id="search" name="search" placeholder="Search" class="form-control" />
<input type="hidden" name="submit_button" value="search" />
</form>

<table class="table table-condensed table-striped table-hover">
<thead>
<tr>
<th><a href="?order=name">Name</a></th>
<th><a href="?order=owner_id">Owner</a></th>
<th><a href="?order=external">External</a></th>
<th><a href="?order=privacy_law">Privacy law</a></th>
</tr>
</thead>
<tbody>
<xsl:for-each select="applications/application">
<tr class="click" onClick="javascript:document.location='/{/output/page}/{@id}'">
<td><xsl:value-of select="name" /></td>
<td><xsl:value-of select="owner" /></td>
<td><xsl:value-of select="external" /></td>
<td><xsl:value-of select="privacy_law" /></td>
</tr>
</xsl:for-each>
</tbody>
</table>

<div class="right">
<xsl:apply-templates select="pagination" />
</div>

<div class="btn-group left">
<a href="/{/output/page}/new" class="btn btn-default">New application</a>
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
<xsl:if test="application/@id">
<input type="hidden" name="id" value="{application/@id}" />
</xsl:if>

<label for="name">Name:</label>
<input type="text" id="name" name="name" value="{application/name}" class="form-control" />
<label for="description">Description:</label>
<textarea id="description" name="description" class="form-control"><xsl:value-of select="application/description" /></textarea>
<label for="owner">Owner:</label>
<span class="owner_type"><input type="radio" name="owner_type" value="new" checked="checked" onChange="javascript:set_owner_type()" />New<input type="radio" name="owner_type" value="existing" onChange="javascript:set_owner_type()"><xsl:if test="business/@owner='existing'"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if></input>Existing</span>
<div id="owner_type"><input type="text" id="owner_name" name="owner_name" value="{application/owner_name}" class="form-control" />
<select id="owner_id" name="owner_id" class="form-control">
<xsl:for-each select="business/item">
<option value="{@id}"><xsl:if test="@id=../../application/owner_id"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if><xsl:value-of select="." /></option>
</xsl:for-each>
</select></div>
<label for="confidentiality">Confidentiality:</label>
<select id="confidentiality" name="confidentiality" class="form-control">
<xsl:for-each select="confidentiality/value">
<option value="{position()}"><xsl:if test="position()=../../application/confidentiality"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if><xsl:value-of select="." /></option>
</xsl:for-each>
</select>
<label for="integrity">Integrity:</label>
<select id="integrity" name="integrity" class="form-control">
<xsl:for-each select="integrity/value">
<option value="{position()}"><xsl:if test="position()=../../application/integrity"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if><xsl:value-of select="." /></option>
</xsl:for-each>
</select>
<label for="availability">Availability:</label>
<select id="availability" name="availability" class="form-control">
<xsl:for-each select="availability/value">
<option value="{position()}"><xsl:if test="position()=../../application/availability"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if><xsl:value-of select="." /></option>
</xsl:for-each>
</select>
<label for="external">External application:</label>
<input type="checkbox" id="external" name="external"><xsl:if test="application/external='yes'"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if></input>
<label for="privacy_law">Privacy law applicable:</label>
<input type="checkbox" id="privacy_law" name="privacy_law"><xsl:if test="application/privacy_law='yes'"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if></input>

<div class="btn-group">
<input type="submit" name="submit_button" value="Save application" class="btn btn-default" />
<a href="/{/output/page}" class="btn btn-default">Cancel</a>
<xsl:if test="application/@id">
<input type="submit" name="submit_button" value="Delete application" class="btn btn-default" onClick="javascript:return confirm('DELETE: Are you sure?')" />
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
<img src="/images/icons/application.png" class="title_icon" />
<h1>Applications</h1>
<xsl:apply-templates select="overview" />
<xsl:apply-templates select="edit" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
