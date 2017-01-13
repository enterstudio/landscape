<?xml version="1.0" ?>
<xsl:stylesheet	version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="active_poll">
<div class="active_poll panel panel-default">
<div class="panel-heading"><xsl:value-of select="question" /></div>
<div class="panel-body">
<form action="{/output/page/@url}" method="post">
<ul class="answers">
<xsl:for-each select="answers/answer">
	<li>
	<xsl:choose>
		<xsl:when test="../../@can_vote='yes'">
			<input type="radio" name="vote" value="{@id}" /><xsl:value-of select="." />
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="answer" /> - <xsl:value-of select="percentage" />%
			<div class="percentage" style="width:{percentage}px" />
		</xsl:otherwise>
	</xsl:choose>
	</li>
</xsl:for-each>
</ul>
<xsl:if test="answers/@votes">
<p>Number of votes: <xsl:value-of select="answers/@votes" /></p>
</xsl:if>

<xsl:if test="@can_vote='yes'">
<input type="submit" name="submit_button" value="Vote" class="btn btn-default" />
</xsl:if>
</form>

<p class="enddate">Poll ending at <xsl:value-of select="end_date" />.</p>
</div>
</div>
</xsl:template>

</xsl:stylesheet>
